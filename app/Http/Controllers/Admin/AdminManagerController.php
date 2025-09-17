<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\ManagerDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminManagerCreateRequest;
use App\Http\Requests\Admin\AdminManagerUpdateRequest;
use App\Models\College;
use App\Models\Country;
use App\Models\DocumentCategory;
use App\Models\Role;
use App\Models\User;
use App\Models\UserStatus;
use App\Traits\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminManagerController extends Controller
{
    use FileUpload;

    public function index(ManagerDataTable $dataTable)
    {
        $managers = User::whereHas('mainRoleRelation', fn ($q) => $q->where('name', 'manager'))
        ->with('userStatus') 
        ->orderBy('name', 'ASC')
        ->get();

        return $dataTable->render('admin.manager.index', compact('managers'));
    }

    public function create()
    {
        $documentCategories = DocumentCategory::where(['role_id' => 9, 'status' => 1])->get(); 
        $countries = Country::where('status', 1)->orderBy('name')->get();
        $userStatuses = UserStatus::where('status', 1)->orderBy('name', 'ASC')->get();
        $colleges = College::where('status', 1)->get();
        return view ('admin.manager.create', compact('documentCategories','countries','userStatuses','colleges'));
    }

    public function store(AdminManagerCreateRequest $request)
    {
        $manager = new User();

        // Upload profile image if present
        if ($request->hasFile('image')) {
            $imagePath = $this->uploadFile($request->file('image'), 'uploads/profile-images', 'image');
            $manager->image = $imagePath;
        }

        // Assign basic fields
        $manager->college_id = $request->college_id;
        $manager->name = $request->name;
        $manager->gender = strtolower($request->gender);
        $manager->phone = $request->phone;
        $manager->email = $request->email;
        $manager->contact_email = $request->contact_email ?? $request->email;
        $manager->dob = $request->dob;
        $manager->post_code = $request->post_code;
        $manager->city = $request->city;
        $manager->country_id = $request->country_id;
        $manager->address = $request->address;
        $manager->bio = $request->bio;
        $manager->account_status = $request->account_status;
        $manager->user_status_id = $request->user_status_id;

        // Set password (required for create)
        $manager->password = bcrypt($request->password);

        $manager->save();

        // NEW: Assign roles and sync permissions using the helper method
        $this->attachRolesAndPermissions($manager, [9], 9);

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

                        $manager->documents()->create([
                            'category_id' => $categoryId,
                            'path' => $filePath,
                            'date' => $document['date'] ?? null,
                        ]);
                    }
                }
            }
        }

        if ($request->has('user_notes')) {
            foreach ($request->user_notes as $noteInput) {
                if (!empty($noteInput['note'])) {
                    $manager->userNotes()->create([
                        'note'     => $noteInput['note'],
                        'added_by' => Auth::id(),
                    ]);
                }
            }
        }

        notyf()->success('Manager created successfully!');
        return redirect()->route('admin.manager.index');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $manager = User::findOrFail($id);
        $documentCategories = DocumentCategory::where(['role_id' => 9, 'status' => 1])->get(); 
        $countries = Country::where('status', 1)->orderBy('name')->get();
        $userStatuses = UserStatus::where('status', 1)->orderBy('name', 'ASC')->get();
        $colleges = College::where('status', 1)->get();

        return view('admin.manager.edit', compact('manager','documentCategories','countries','userStatuses','colleges'));
    }

    public function update(AdminManagerUpdateRequest $request, User $manager)
    {
        if($request->hasFile('image')){
            $imagePath = $this->uploadFile($request->file('image'), 'uploads/profile-images', 'image');
            $this->deleteFile($manager->image);
            $manager->image = $imagePath;
        }

        if ($request->filled('deleted_documents')) {
            $deletedIds = explode(',', $request->deleted_documents);
            foreach ($deletedIds as $docId) {
                $doc = $manager->documents()->find($docId);
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

                        $manager->documents()->create([
                            'category_id' => $categoryId,
                            'path' => $filePath,
                            'date' => !empty($document['date']) ? $document['date'] : null,
                        ]);
                    }
                }
            }
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
                    $existing = $manager->userNotes()->where('id', $noteInput['id'])->first();
                    if ($existing) {
                        $existing->update([
                            'note' => $noteInput['note'],
                        ]);
                        $submittedNoteIds[] = $existing->id;
                    }
                } else {
                    // Create new note
                    $new = $manager->userNotes()->create([
                        'note'     => $noteInput['note'],
                        'added_by' => Auth::id(),
                    ]);
                    $submittedNoteIds[] = $new->id;
                }
            }

            // Delete removed notes
            $manager->userNotes()->whereNotIn('id', $submittedNoteIds)->delete();
        } else {
            // No notes submitted; delete all
            $manager->userNotes()->delete();
        }

        $manager->college_id = $request->college_id;
        $manager->name = $request->name;
        $manager->gender = strtolower($request->gender);
        $manager->phone = $request->phone;
        $manager->email = $request->email;
        $manager->contact_email = $request->contact_email ?? $request->email;
        $manager->dob = $request->dob;
        $manager->post_code = $request->post_code;
        $manager->city = $request->city;
        $manager->country_id = $request->country_id;
        $manager->address = $request->address;
        $manager->bio = $request->bio;
        $manager->account_status = $request->account_status;
        $manager->user_status_id = $request->user_status_id;

        if ($request->filled('password')) {
            $manager->password = bcrypt($request->password);
        }
        
        $manager->save();

        // NEW: Apply the same logic for roles and permissions as the store method
        $this->attachRolesAndPermissions($manager, [9], 9);

        notyf()->success('Manager updated successfully!');

        if ($request->input('action') === 'save_stay') {
            return redirect()->back();
        } else {
            return redirect()->route('admin.manager.index');
        }
    }

    public function destroy(string $id)
    {
        $manager = User::findOrFail($id);

        // Block deletion if manager has documents
        if ($manager->documents()->exists()) {
            return response(['status' => 'error', 'message' => 'You cannot delete an manager who has documents!'], 500);
        }

        try {
            $this->deleteFile($manager->image);
            $manager->delete();
            return response(['status' => 'success', 'message' => 'Manager deleted successfully!'], 200);
        } catch (\Exception $e) {
            return response(['status' => 'error', 'message' => 'Something went wrong!'], 500);
        }
    }

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
