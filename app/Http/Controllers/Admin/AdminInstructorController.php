<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\InstructorDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminInstructorCreateRequest;
use App\Http\Requests\Admin\AdminInstructorUpdateRequest;
use App\Models\College;
use App\Models\Country;
use App\Models\DocumentCategory;
use App\Models\Role;
use App\Models\SocialPlatform;
use App\Models\User;
use App\Models\UserStatus;
use App\Traits\FileUpload;
use Illuminate\Support\Facades\Auth;

class AdminInstructorController extends Controller
{
    use FileUpload;

    public function index(InstructorDataTable $dataTable)
    {
        $instructors = User::whereHas('mainRoleRelation', fn ($q) => $q->where('name', 'instructor'))
        ->with('groups.students','userStatus') 
        ->orderBy('name', 'ASC')
        ->get();

        foreach ($instructors as $instructor) {
            $allStudents = $instructor->groups->flatMap->students;
            $instructor->studentAccountStatuses = $allStudents->groupBy('account_status')->map->count();
            $instructor->studentUserStatuses = $allStudents->groupBy('user_status')->map->count();
            $instructor->totalStudents = $allStudents->count();
        } 

        return $dataTable->render('admin.instructor.index', compact('instructors'));
    }


    public function create()
    {
        $documentCategories = DocumentCategory::where(['role_id' => 3, 'status' => 1])->get(); 
        $socialPlatforms = SocialPlatform::where('status', 1)->get();
        $countries = Country::where('status', 1)->orderBy('name')->get();
        $userStatuses = UserStatus::where('status', 1)->orderBy('name', 'ASC')->get();
        $colleges = College::where('status', 1)->get();
        return view ('admin.instructor.create',compact('documentCategories','socialPlatforms','countries','userStatuses','colleges'));
    }

    public function store(AdminInstructorCreateRequest $request)
    {
        $instructor = new User();

        // Upload profile image if present
        if ($request->hasFile('image')) {
            $imagePath = $this->uploadFile($request->file('image'), 'uploads/profile-images', 'image');
            $instructor->image = $imagePath;
        }

        // Assign basic fields
        $instructor->college_id = $request->college_id;
        $instructor->name = $request->name;
        $instructor->gender = strtolower($request->gender);
        $instructor->phone = $request->phone;
        $instructor->email = $request->email;
        $instructor->contact_email = $request->contact_email ?? $request->email;
        $instructor->dob = $request->dob;
        $instructor->post_code = $request->post_code;
        $instructor->city = $request->city;
        $instructor->country_id = $request->country_id;
        $instructor->address = $request->address;
        $instructor->bio = $request->bio;
        $instructor->account_status = $request->account_status;
        $instructor->user_status_id = $request->user_status_id;

        // Set password (required for create)
        $instructor->password = bcrypt($request->password);

        $instructor->save();

        // NEW: Assign roles and sync permissions using the helper method
        $this->attachRolesAndPermissions($instructor, [3], 3);

        // ✅ Save user documents (if any)
        if ($request->has('documents')) {
            foreach ($request->documents as $document) {
                if (isset($document['file']) && $document['file'] instanceof \Illuminate\Http\UploadedFile) {
                    $categoryId = $document['category_id'];
                    $category = DocumentCategory::find($categoryId);

                    if ($category) {
                        $prefix = \Str::slug($category->name);
                        $uniqueName = $prefix . '_' . uniqid();
                        $extension = $document['file']->getClientOriginalExtension();
                        $filename = $uniqueName . '.' . $extension;

                        $document['file']->move(public_path('uploads/user-documents'), $filename);
                        $filePath = 'uploads/user-documents/' . $filename;

                        $instructor->documents()->create([
                            'category_id' => $categoryId,
                            'path' => $filePath,
                            'date' => $document['date'] ?? null,
                        ]);
                    }
                }
            }
        }

        // ✅ Save social accounts (if any)
        if ($request->has('social_accounts')) {
            foreach ($request->social_accounts as $account) {
                if (!empty($account['platform_id']) && !empty($account['link'])) {
                    $instructor->socialAccounts()->create([
                        'social_platform_id' => $account['platform_id'],
                        'link' => $account['link'],
                    ]);
                }
            }
        }

        if ($request->has('user_notes')) {
            foreach ($request->user_notes as $noteInput) {
                if (!empty($noteInput['note'])) {
                    $instructor->userNotes()->create([
                        'note'     => $noteInput['note'],
                        'added_by' => Auth::id(),
                    ]);
                }
            }
        }

        notyf()->success('Instructor created successfully!');
        return redirect()->route('admin.instructor.index');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $instructor = User::findOrFail($id);
        $documentCategories = DocumentCategory::where(['role_id' => 3, 'status' => 1])->get(); 
        $socialPlatforms = SocialPlatform::where('status', 1)->get();
        $countries = Country::where('status', 1)->orderBy('name')->get();
        $userStatuses = UserStatus::where('status', 1)->orderBy('name', 'ASC')->get();
        $colleges = College::where('status', 1)->get();

        return view('admin.instructor.edit', compact('instructor','documentCategories','socialPlatforms','countries','userStatuses','colleges'));
    }

    public function update(AdminInstructorUpdateRequest $request, User $instructor)
    {

        if($request->hasFile('image')){
            $imagePath = $this->uploadFile($request->file('image'), 'uploads/profile-images', 'image');
            $this->deleteFile($instructor->image);
            $instructor->image = $imagePath;
        }

        if ($request->filled('deleted_documents')) {
            $deletedIds = explode(',', $request->deleted_documents);
            foreach ($deletedIds as $docId) {
                $doc = $instructor->documents()->find($docId);
                if ($doc) {
                    $this->deleteFile($doc->path); // optional: delete file from disk
                    $doc->delete();
                }
            }
        }

        // Handle newly uploaded documents (from dynamic table)
        if ($request->has('documents')) {
            foreach ($request->documents as $document) {
                if (isset($document['file']) && $document['file'] instanceof \Illuminate\Http\UploadedFile) {
                    $categoryId = $document['category_id'];
                    $category = DocumentCategory::find($categoryId);

                    if ($category) {
                        $prefix = \Str::slug($category->name); // e.g., "id_card"
                        $uniqueName = $prefix . '_' . uniqid();
                        $extension = $document['file']->getClientOriginalExtension();
                        $filename = $uniqueName . '.' . $extension;

                        // Store file under public/uploads/user-documents
                        $document['file']->move(public_path('uploads/user-documents'), $filename);

                        // Save relative path (used with asset())
                        $filePath = 'uploads/user-documents/' . $filename;

                        $instructor->documents()->create([
                            'category_id' => $categoryId,
                            'path' => $filePath,
                            'date' => !empty($document['date']) ? $document['date'] : null,
                        ]);
                    }
                }
            }
        }

        // Sync social platforms
        if ($request->has('social_accounts')) {
            $submittedIds = [];
            foreach ($request->social_accounts as $account) {
                // Skip incomplete rows
                if (empty($account['platform_id']) || empty($account['link'])) {
                    continue;
                }

                if (!empty($account['id'])) {
                    // Update existing
                    $existing = $instructor->socialAccounts()->where('id', $account['id'])->first();
                    if ($existing) {
                        $existing->update([
                            'social_platform_id' => $account['platform_id'],
                            'link' => $account['link'],
                        ]);
                        $submittedIds[] = $existing->id;
                    }
                } else {
                    // Create new
                    $new = $instructor->socialAccounts()->create([
                        'social_platform_id' => $account['platform_id'],
                        'link' => $account['link'],
                    ]);
                    $submittedIds[] = $new->id;
                }
            }

            // Delete removed accounts
            $instructor->socialAccounts()->whereNotIn('id', $submittedIds)->delete();
        } else {
            // If no accounts submitted, delete all
            $instructor->socialAccounts()->delete();
        }

        // Sync user notes
        if ($request->has('user_notes')) {
            $submittedNoteIds = [];

            foreach ($request->user_notes as $noteInput) {
                // Skip empty notes
                if (empty($noteInput['note'])) {
                    continue;
                }

                if (!empty($noteInput['id'])) {
                    // Update existing note
                    $existing = $instructor->userNotes()->where('id', $noteInput['id'])->first();
                    if ($existing) {
                        $existing->update([
                            'note' => $noteInput['note'],
                        ]);
                        $submittedNoteIds[] = $existing->id;
                    }
                } else {
                    // Create new note
                    $new = $instructor->userNotes()->create([
                        'note'     => $noteInput['note'],
                        'added_by' => Auth::id(),
                    ]);
                    $submittedNoteIds[] = $new->id;
                }
            }

            // Delete removed notes
            $instructor->userNotes()->whereNotIn('id', $submittedNoteIds)->delete();
        } else {
            // No notes submitted; delete all
            $instructor->userNotes()->delete();
        }

        $instructor->college_id = $request->college_id;
        $instructor->name = $request->name;
        $instructor->gender = strtolower($request->gender);
        $instructor->phone = $request->phone;
        $instructor->email = $request->email;
        $instructor->contact_email = $request->contact_email ?? $request->email;
        $instructor->dob = $request->dob;
        $instructor->post_code = $request->post_code;
        $instructor->city = $request->city;
        $instructor->country_id = $request->country_id;
        $instructor->address = $request->address;
        $instructor->bio = $request->bio;
        $instructor->account_status = $request->account_status;
        $instructor->user_status_id = $request->user_status_id;

        if ($request->filled('password')) {
            $instructor->password = bcrypt($request->password);
        }
        
        $instructor->save();

        // NEW: Apply the same logic for roles and permissions as the store method
        $this->attachRolesAndPermissions($instructor, [3], 3);

        notyf()->success('Instructor updated successfully!');

        if ($request->input('action') === 'save_stay') {
            return redirect()->back();
        } else {
            return redirect()->route('admin.instructor.index');
        }
        
    }

    public function destroy(string $id)
    {
        $instructor = User::findOrFail($id);

        // Block deletion if instructor has groups
        if ($instructor->groups()->exists()) {
            return response(['status' => 'error', 'message' => 'You cannot delete an instructor who has groups!'], 500);
        }

        // Block deletion if instructor has documents
        if ($instructor->documents()->exists()) {
            return response(['status' => 'error', 'message' => 'You cannot delete an instructor who has documents!'], 500);
        }

        try {
            $this->deleteFile($instructor->image);
            $instructor->delete();
            return response(['status' => 'success', 'message' => 'Instructor deleted successfully!'], 200);
        } catch (\Exception $e) {
            return response(['status' => 'error', 'message' => 'Something went wrong!'], 500);
        }
    }


    /**
     * Helper method to attach roles and sync permissions.
     */
    protected function attachRolesAndPermissions(User $user, array $roleIds, int $mainRoleId)
    {
        // First, sync the roles with the user, setting the main role.
        $pivotData = [];
        foreach ($roleIds as $roleId) {
            $pivotData[$roleId] = ['is_main' => ($roleId == $mainRoleId)];
        }
        $user->roles()->sync($pivotData);

        // Fetch all unique permission IDs from the selected roles.
        $permissions = Role::whereIn('id', $roleIds)
                            ->with('permissions')
                            ->get()
                            ->flatMap(fn ($role) => $role->permissions)
                            ->pluck('id')
                            ->unique();

        // Finally, sync these permissions directly to the user_permissions table.
        // This removes any old permissions and adds the new ones.
        $user->directPermissions()->sync($permissions);
    }
    
}
