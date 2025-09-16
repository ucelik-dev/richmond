<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\UserDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminUserCreateRequest;
use App\Http\Requests\Admin\AdminUserUpdateRequest;
use App\Models\AwardingBody;
use App\Models\Batch;
use App\Models\Country;
use App\Models\Course;
use App\Models\CourseLevel;
use App\Models\DocumentCategory;
use App\Models\Group;
use App\Models\Permission;
use App\Models\Role;
use App\Models\SocialPlatform;
use App\Models\User;
use App\Models\UserStatus;
use App\Traits\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminUserController extends Controller
{
    use FileUpload;
    
    public function index(UserDataTable $dataTable)
    {
        $users = User::with(['enrollments.course','enrollments.group','enrollments.batch','userStatus'])->orderBy('name', 'ASC')->get();
        return $dataTable->render('admin.user.index', compact('users'));
    }

    public function create()
    {
        $courses = Course::all();
        $roles = Role::all();
        $awardingBodies = AwardingBody::where('status', 1)->get();
        $countries = Country::where('status', 1)->orderBy('name')->get();

        $salesUsers = User::whereHas('mainRoleRelation', fn ($q) => $q->where('name', 'sales'))
            ->where('account_status', 1)
            ->orderBy('name')
            ->get();

        $agentUsers = User::whereHas('mainRoleRelation', fn ($q) => $q->where('name', 'agent'))
            ->where('account_status', 1)
            ->orderBy('company', 'ASC')
            ->get();

        $managerUsers = User::whereHas('roles', fn ($q) => $q->where('name', 'manager'))
            ->where('account_status', 1)
            ->orderBy('company', 'ASC')
            ->get();

        $userStatuses = UserStatus::where('status', 1)->orderBy('name', 'ASC')->get();
        $groups = Group::where('status', 1)->orderBy('name', 'ASC')->get();
        $batches = Batch::where('status', 1)->orderBy('name', 'ASC')->get();
        $documentCategories = DocumentCategory::where('status', 1)->get(); 
        $socialPlatforms = SocialPlatform::where('status', 1)->get();

        return view('admin.user.create', compact('courses','roles','awardingBodies','salesUsers','agentUsers','managerUsers','userStatuses','groups','batches','documentCategories','socialPlatforms','countries'));
    }

    public function store(AdminUserCreateRequest $request)  
    {
        $user = new User();

        // Upload profile image if present
        if ($request->hasFile('image')) {
            $user->image = $this->uploadFile($request->file('image'), 'uploads/profile-images', 'image');
        }

        // Assign base data
        $user->name = $request->name;
        $user->gender = strtolower($request->gender);
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->contact_email = $request->contact_email ?? $request->email;
        $user->dob = $request->dob;
        $user->post_code = $request->post_code;
        $user->city = $request->city;
        $user->country_id = $request->country_id;
        $user->address = $request->address;
        $user->bio = $request->bio;
        $user->account_status = $request->account_status;
        $user->user_status_id = $request->user_status_id;

        // Registration source info
        $user->sales_person_id = $request->sales_person_id;
        $user->agent_id = $request->agent_id;
        $user->manager_id = $request->manager_id;

        // Password
        $user->password = bcrypt($request->password);

        $user->save();

        // The single call to the helper method handles both roles and permissions
        if ($request->filled('roles') && is_array($request->roles) && $request->filled('main_role_id')) {
            $this->attachRolesAndPermissions($user, $request->roles, $request->main_role_id);
        }

        if($request->agent_code){
            $user->agentProfile()->create([
                'user_id' => $user->id,
                'agent_code' => $request->agent_code,
                'commission_percent' => $request->commission_percent,
                'commission_amount' => $request->commission_amount,
                'discount_percent' => $request->discount_percent,
                'discount_amount' => $request->discount_amount,
            ]);
        }

        ///////////////////////////////////////////////////////////
        //////////////////// COURSE ENROLLMENTS ///////////////////
        ///////////////////////////////////////////////////////////

        $enrolledCourses = [];

        if ($request->filled('enrollments')) {
            foreach ($request->enrollments as $enrollment) {
                if (!empty($enrollment['course_id'])) {
                    $user->enrollments()->create([
                        'course_id' => $enrollment['course_id'],
                        'group_id' => $enrollment['group_id'] ?? null,
                        'batch_id' => $enrollment['batch_id'] ?? null,
                        'status' => 'active',
                        'enrolled_at' => now(),
                    ]);

                    $enrolledCourses[] = $enrollment['course_id'];
                }
            }
        }



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

                        $user->documents()->create([
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
                    $user->socialAccounts()->create([
                        'social_platform_id' => $account['platform_id'],
                        'link' => $account['link'],
                    ]);
                }
            }
        }

        if ($request->has('user_notes')) {
            foreach ($request->user_notes as $noteInput) {
                if (!empty($noteInput['note'])) {
                    $user->userNotes()->create([
                        'note'     => $noteInput['note'],
                        'added_by' => Auth::id(),
                    ]);
                }
            }
        }

        notyf()->success('User created successfully!');
        return redirect()->route('admin.user.index');
    }

    public function show(string $id)
    {
        
    }

    public function edit(string $id)
    {
        $user = User::with(['roles' => function ($q) {
            $q->withPivot('is_main');
        }, 'enrollments'])->findOrFail($id);

        $isAgent = $user->roles()
            ->wherePivot('is_main', true)
            ->where('role_id', 4)
            ->exists();

        $awardingBodies = AwardingBody::where('status', 1)->get();
        $countries = Country::where('status', 1)->orderBy('name')->get();

        $salesUsers = User::whereHas('mainRoleRelation', fn ($q) => $q->where('name', 'sales'))
            ->where('account_status', 1)
            ->orderBy('name')
            ->get();

        $agentUsers = User::whereHas('mainRoleRelation', fn ($q) => $q->where('name', 'agent'))
            ->where('account_status', 1)
            ->orderBy('company', 'ASC')
            ->get();

        $managerUsers = User::whereHas('roles', fn ($q) => $q->where('name', 'manager'))
            ->where('account_status', 1)
            ->orderBy('company', 'ASC')
            ->get();

        $selectedRoles = $user->roles->pluck('id')->toArray();
        $mainRoleId = optional($user->roles->firstWhere('pivot.is_main', true))->id;

        $enrolledCourseIds = $user->enrollments->pluck('course_id')->toArray();
        $courses = Course::all(); 
        $courseLevels = CourseLevel::where('status', 1)->whereNot('id', 8)->orderBy('name', 'ASC')->get();
        $userStatuses = UserStatus::where('status', 1)->orderBy('name', 'ASC')->get();
        $groups = Group::where('status', 1)->orderBy('name', 'ASC')->get();
        $batches = Batch::where('status', 1)->orderBy('name', 'ASC')->get();
        $documentCategories = DocumentCategory::where('status', 1)->get(); 
        $socialPlatforms = SocialPlatform::where('status', 1)->get();

        return view('admin.user.edit', [
            'user' => $user,
            'roles' => Role::all(),
            'awardingBodies' => $awardingBodies,
            'salesUsers' => $salesUsers,
            'agentUsers' => $agentUsers,
            'managerUsers' => $managerUsers,
            'selectedRoles' => $selectedRoles,
            'mainRoleId' => $mainRoleId,
            'enrolledCourseIds' => $enrolledCourseIds,
            'courses' => $courses,
            'courseLevels' => $courseLevels,
            'userStatuses' => $userStatuses,
            'groups' => $groups,
            'batches' => $batches,
            'documentCategories' => $documentCategories,
            'socialPlatforms' => $socialPlatforms,
            'countries' => $countries,
            'isAgent' => $isAgent,
        ]);
    }

    public function update(AdminUserUpdateRequest $request, User $user)
    {
        // Handle profile image (still in users table)
        if ($request->hasFile('image')) {
            $imagePath = $this->uploadFile($request->file('image'), 'uploads/profile-images', 'image');
            $this->deleteFile($user->image);
            $user->image = $imagePath;
        }

        if ($request->filled('deleted_documents')) {
            $deletedIds = explode(',', $request->deleted_documents);
            foreach ($deletedIds as $docId) {
                $doc = $user->documents()->find($docId);
                if ($doc) {
                    $this->deleteFile($doc->path); // optional: delete file from disk
                    $doc->delete();
                }
            }
        }

        ///////////////////////////////////////////////////////////
        //////////////////////// DOCUMENTS ////////////////////////
        ///////////////////////////////////////////////////////////

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

                        $user->documents()->create([
                            'category_id' => $categoryId,
                            'path' => $filePath,
                            'date' => !empty($document['date']) ? $document['date'] : null,
                        ]);
                    }
                }
            }
        }








        ///////////////////////////////////////////////////////////
        ///////////////////// SOCIAL PLATFORMS ////////////////////
        ///////////////////////////////////////////////////////////

        if ($request->has('social_accounts')) {
            $submittedSocialAccountIds = [];

            foreach ($request->social_accounts as $account) {
                // Skip if platform or link missing
                if (empty($account['platform_id']) || empty($account['link'])) {
                    continue;
                }

                if (!empty($account['id'])) {
                    // Check if existing and update only if changed
                    $existing = $user->socialAccounts()->where('id', $account['id'])->first();
                    if ($existing) {
                        if (
                            $existing->social_platform_id != $account['platform_id'] ||
                            $existing->link !== $account['link']
                        ) {
                            $existing->update([
                                'social_platform_id' => $account['platform_id'],
                                'link' => $account['link'],
                            ]);
                        }
                        $submittedSocialAccountIds[] = $existing->id;
                    }
                } else {
                    // Create new account
                    $new = $user->socialAccounts()->create([
                        'social_platform_id' => $account['platform_id'],
                        'link' => $account['link'],
                    ]);
                    $submittedSocialAccountIds[] = $new->id;
                }
            }

            // Delete accounts that were removed in the form
            $user->socialAccounts()->whereNotIn('id', $submittedSocialAccountIds)->delete();

        } else {
            // No accounts submitted — delete all
            $user->socialAccounts()->delete();
        }







        ///////////////////////////////////////////////////////////
        //////////////////////// USER NOTES ///////////////////////
        ///////////////////////////////////////////////////////////

        if ($request->has('user_notes')) {
            $submittedNoteIds = [];

            foreach ($request->user_notes as $noteInput) {
                // Skip empty notes
                if (empty($noteInput['note'])) {
                    continue;
                }

                if (!empty($noteInput['id'])) {
                    // Update existing note
                    $existing = $user->userNotes()->where('id', $noteInput['id'])->first();
                    if ($existing) {
                        $existing->update([
                            'note' => $noteInput['note'],
                        ]);
                        $submittedNoteIds[] = $existing->id;
                    }
                } else {
                    // Create new note
                    $new = $user->userNotes()->create([
                        'note'     => $noteInput['note'],
                        'added_by' => Auth::id(),
                    ]);
                    $submittedNoteIds[] = $new->id;
                }
            }

            // Delete removed notes
            $user->userNotes()->whereNotIn('id', $submittedNoteIds)->delete();
        } else {
            // No notes submitted; delete all
            $user->userNotes()->delete();
        }


        // Update main user fields
        $user->name = $request->name;
        $user->gender = strtolower($request->gender);
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->contact_email = $request->contact_email ?? $request->email;
        $user->dob = $request->dob;
        $user->post_code = $request->post_code;
        $user->city = $request->city;
        $user->country_id = $request->country_id;
        $user->address = $request->address;
        $user->bio = $request->bio;
        $user->account_status = $request->account_status;
        $user->user_status_id = $request->user_status_id;

        // Registration & commission info
        $user->sales_person_id = $request->sales_person_id;
        $user->agent_id = $request->agent_id;
        $user->manager_id = $request->manager_id;

        // Password
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        if ($user->roles()->wherePivot('is_main', true)->where('role_id', 4)->exists()) {

            if($request->agent_code){
                $user->agentProfile()->updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'agent_code' => $request->agent_code,
                        'commission_percent' => $request->commission_percent ?? 0,
                        'commission_amount' => $request->commission_amount ?? 0,
                        'discount_percent' => $request->discount_percent ?? 0,
                        'discount_amount' => $request->discount_amount ?? 0,
                    ]
                );
            }

        }

        // Use the helper method for a single, correct sync of roles and permissions
        if ($request->filled('roles') && is_array($request->roles) && $request->filled('main_role_id')) {
            $this->attachRolesAndPermissions($user, $request->roles, $request->main_role_id);
        } else {
            // If no roles are provided, detach all roles AND permissions
            $user->roles()->detach();
            $user->directPermissions()->detach(); // <-- Added this line
        }

        


        
        ///////////////////////////////////////////////////////////
        /////////////// AWARDING BODY REGISTRATIONS ///////////////
        ///////////////////////////////////////////////////////////

        $existingRegistrationIds = $user->awardingBodyRegistrations()->pluck('id')->toArray();
        $incomingRegistrationIds = [];

        if ($request->filled('registrations')) {
            foreach ($request->registrations as $registration) {
                // Skip if all fields are empty
                if (collect($registration)->filter()->isEmpty()) {
                    continue;
                }

                // Ensure required fields are present before update/create
                $hasRequiredFields = isset(
                    $registration['course_id'],
                    $registration['awarding_body_id'],
                    $registration['awarding_body_registration_level_id'],
                    $registration['awarding_body_registration_number'],
                    $registration['awarding_body_registration_date']
                );

                if (!empty($registration['id']) && $hasRequiredFields) {
                    // Update existing
                    $existing = $user->awardingBodyRegistrations()
                        ->where('id', $registration['id'])->first();

                    if ($existing) {
                        $existing->update([
                            'course_id' => $registration['course_id'],
                            'awarding_body_id' => $registration['awarding_body_id'],
                            'awarding_body_registration_level_id' => $registration['awarding_body_registration_level_id'],
                            'awarding_body_registration_number' => $registration['awarding_body_registration_number'],
                            'awarding_body_registration_date' => $registration['awarding_body_registration_date'],
                        ]);
                        $incomingRegistrationIds[] = $registration['id'];
                    }

                } elseif ($hasRequiredFields) {
                    // Create new
                    $new = $user->awardingBodyRegistrations()->create([
                        'user_id' => $user->id,
                        'course_id' => $registration['course_id'],
                        'awarding_body_id' => $registration['awarding_body_id'],
                        'awarding_body_registration_level_id' => $registration['awarding_body_registration_level_id'],
                        'awarding_body_registration_number' => $registration['awarding_body_registration_number'],
                        'awarding_body_registration_date' => $registration['awarding_body_registration_date'],
                    ]);
                    $incomingRegistrationIds[] = $new->id;
                }
            }
        }

        // Delete removed registrations
        $toDelete = array_diff($existingRegistrationIds, $incomingRegistrationIds);
        $user->awardingBodyRegistrations()->whereIn('id', $toDelete)->delete();







        ///////////////////////////////////////////////////////////
        //////////////////// COURSE ENROLLMENTS ///////////////////
        ///////////////////////////////////////////////////////////

        if ($request->filled('enrollments')) {
            $submittedCourseIds = collect($request->input('enrollments'))->pluck('course_id')->filter()->unique()->toArray();

            // Delete enrollments that are no longer present
            $user->enrollments()->whereNotIn('course_id', $submittedCourseIds)->delete();

            // Get existing course_ids
            $existingEnrollments = $user->enrollments()->get()->keyBy('course_id');

            $newCourseIds = []; // ✅ Initialize this to track newly created enrollments

            foreach ($request->enrollments as $enrollment) {
                if (empty($enrollment['course_id'])) {
                    continue;
                }

                $courseId = $enrollment['course_id'];
                $groupId = $enrollment['group_id'] ?? null;
                $batchId = $enrollment['batch_id'] ?? null;

                if ($existingEnrollments->has($courseId)) {
                    // Update existing enrollment
                    $existingEnrollments[$courseId]->update([
                        'group_id' => $groupId,
                        'batch_id' => $batchId,
                    ]);
                } else {
                    // Create new enrollment
                    $user->enrollments()->create([
                        'user_id' => $user->id,
                        'course_id' => $courseId,
                        'group_id' => $groupId,
                        'batch_id' => $batchId,
                        'status' => 'active',
                        'enrolled_at' => now(),
                    ]);
                    $newCourseIds[] = $courseId; // ✅ Track newly added course
                }
            }

            
        }








        notyf()->success('User updated successfully!');
        
        if ($request->input('action') === 'save_stay') {
            return redirect()->back();
        } else {
            return redirect()->route('admin.user.index');
        }

    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        // Block deletion if user has enrollments or payments
        if ($user->enrollments()->exists() || $user->payments()->exists()) {
            return response([
                'status' => 'error', 'message' => 'You cannot delete a user who has enrollments or payments!'], 500);
        }

        try {
            $user->delete();
            return response(['status' => 'success', 'message' => 'User deleted successfully!'], 200);
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







    public function editPermissions(User $user)
    {
        // Get only the roles and their permissions for this specific user
        $roles = $user->roles()->with('permissions')->get();

        // Get the IDs of the permissions the user has directly
        $userPermissions = $user->directPermissions->pluck('id')->toArray();
        
        return view('admin.user.permission.edit', compact('user', 'roles', 'userPermissions'));
    }

    public function updatePermissions(Request $request, User $user)
    {
        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);
        
        // Sync the permissions with the user's direct permissions
        $user->directPermissions()->sync($request->permissions);

        notyf()->success('User permissions updated successfully!');
        
        if ($request->input('action') === 'save_stay') {
            return redirect()->back();
        } else {
            return redirect()->route('admin.user.index');
        }

    }

}
