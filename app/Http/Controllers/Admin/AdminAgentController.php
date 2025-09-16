<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\AgentDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminAgentCreateRequest;
use App\Http\Requests\Admin\AdminAgentUpdateRequest;
use App\Models\Country;
use App\Models\DocumentCategory;
use App\Models\Role;
use App\Models\SocialPlatform;
use App\Models\User;
use App\Models\UserStatus;
use App\Traits\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAgentController extends Controller
{
    use FileUpload;

    public function index(AgentDataTable $dataTable)
    {
        $agents = User::whereHas('mainRoleRelation', fn ($q) => $q->where('name', 'agent'))
            ->with(['assignedStudents.enrollments.course', 'commissions.payment.user','userStatus']) 
            ->orderBy('name', 'ASC')
            ->get(); 

        return $dataTable->render('admin.agent.index', compact('agents'));
    }

    public function create()
    {
        $countries = Country::where('status', 1)->orderBy('name')->get();
        $documentCategories = DocumentCategory::where(['role_id' => 4, 'status' => 1])->get(); 
        $socialPlatforms = SocialPlatform::where('status', 1)->get();
        $userStatuses = UserStatus::where('status', 1)->orderBy('name', 'ASC')->get();
        return view ('admin.agent.create', compact('documentCategories','socialPlatforms','countries','userStatuses'));
    }

    public function store(AdminAgentCreateRequest $request)
    {
        $agent = new User();

        // Upload profile image if present
        if ($request->hasFile('image')) {
            $imagePath = $this->uploadFile($request->file('image'), 'uploads/profile-images', 'image');
            $agent->image = $imagePath;
        }

        // Assign basic fields
        $agent->name = $request->name;
        $agent->phone = $request->phone;
        $agent->email = $request->email;
        $agent->contact_email = $request->contact_email ?? $request->email;
        $agent->post_code = $request->post_code;
        $agent->city = $request->city;
        $agent->country_id = $request->country_id;
        $agent->address = $request->address;
        $agent->company = $request->company;
        $agent->account_status = $request->account_status;
        $agent->user_status_id = $request->user_status_id;

        // Set password (required for create)
        $agent->password = bcrypt($request->password);

        $agent->save();

        // NEW: Use helper to attach roles and sync permissions
        $this->attachRolesAndPermissions($agent, [4], 4);

        if ($agent->roles()->wherePivot('is_main', true)->where('role_id', 4)->exists()) {

            if($request->agent_code){
                $agent->agentProfile()->create([
                    'user_id' => $agent->id,
                    'agent_code' => $request->agent_code,
                    'commission_percent' => $request->commission_percent,
                    'commission_amount' => $request->commission_amount,
                    'discount_percent' => $request->discount_percent,
                    'discount_amount' => $request->discount_amount,
                ]);
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

                        $agent->documents()->create([
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
                    $agent->socialAccounts()->create([
                        'social_platform_id' => $account['platform_id'],
                        'link' => $account['link'],
                    ]);
                }
            }
        }

        if ($request->has('user_notes')) {
            foreach ($request->user_notes as $noteInput) {
                if (!empty($noteInput['note'])) {
                    $agent->userNotes()->create([
                        'note'     => $noteInput['note'],
                        'added_by' => Auth::id(),
                    ]);
                }
            }
        }

        // Assign roles and set 'is_main' to true for the agent role (assuming role ID 4 is 'agent')
        // If an agent should always have role ID 4 as their main role:
        $agent->roles()->sync([4 => ['is_main' => true]]);

        notyf()->success('Agent created successfully!');
        return redirect()->route('admin.agent.index');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $agent = User::findOrFail($id);
        $countries = Country::where('status', 1)->orderBy('name')->get();
        $documentCategories = DocumentCategory::where(['role_id' => 4, 'status' => 1])->get();  
        $socialPlatforms = SocialPlatform::where('status', 1)->get();
        $userStatuses = UserStatus::where('status', 1)->orderBy('name', 'ASC')->get();
        $isAgent = $agent->roles()
            ->wherePivot('is_main', true)
            ->where('role_id', 4)
            ->exists();

        return view('admin.agent.edit', compact('agent','documentCategories','socialPlatforms','countries','userStatuses','isAgent'));
    }

    public function update(AdminAgentUpdateRequest $request, User $agent)
    {

        if($request->hasFile('image')){
            $imagePath = $this->uploadFile($request->file('image'), 'uploads/profile-images', 'image');
            $this->deleteFile($agent->image);
            $agent->image = $imagePath;
        }

        if ($request->filled('deleted_documents')) {
            $deletedIds = explode(',', $request->deleted_documents);
            foreach ($deletedIds as $docId) {
                $doc = $agent->documents()->find($docId);
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

                        $agent->documents()->create([
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
                    $existing = $agent->socialAccounts()->where('id', $account['id'])->first();
                    if ($existing) {
                        $existing->update([
                            'social_platform_id' => $account['platform_id'],
                            'link' => $account['link'],
                        ]);
                        $submittedIds[] = $existing->id;
                    }
                } else {
                    // Create new
                    $new = $agent->socialAccounts()->create([
                        'social_platform_id' => $account['platform_id'],
                        'link' => $account['link'],
                    ]);
                    $submittedIds[] = $new->id;
                }
            }

            // Delete removed accounts
            $agent->socialAccounts()->whereNotIn('id', $submittedIds)->delete();
        } else {
            // If no accounts submitted, delete all
            $agent->socialAccounts()->delete();
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
                    $existing = $agent->userNotes()->where('id', $noteInput['id'])->first();
                    if ($existing) {
                        $existing->update([
                            'note' => $noteInput['note'],
                        ]);
                        $submittedNoteIds[] = $existing->id;
                    }
                } else {
                    // Create new note
                    $new = $agent->userNotes()->create([
                        'note'     => $noteInput['note'],
                        'added_by' => Auth::id(),
                    ]);
                    $submittedNoteIds[] = $new->id;
                }
            }

            // Delete removed notes
            $agent->userNotes()->whereNotIn('id', $submittedNoteIds)->delete();
        } else {
            // No notes submitted; delete all
            $agent->userNotes()->delete();
        }


        $agent->name = $request->name;
        $agent->phone = $request->phone;
        $agent->email = $request->email;
        $agent->contact_email = $request->contact_email ?? $request->email;
        $agent->post_code = $request->post_code;
        $agent->city = $request->city;
        $agent->country_id = $request->country_id;
        $agent->address = $request->address;
        $agent->company = $request->company;
        $agent->account_status = $request->account_status;
        $agent->user_status_id = $request->user_status_id;


        if ($request->filled('password')) {
            $agent->password = bcrypt($request->password);
        }
        
        $agent->save();

        // NEW: Use helper to attach roles and sync permissions
        $this->attachRolesAndPermissions($agent, [4], 4);

        if($request->agent_code){
            $agent->agentProfile()->updateOrCreate(
                ['user_id' => $agent->id],
                [
                    'agent_code' => $request->agent_code,
                    'commission_percent' => $request->commission_percent ?? 0,
                    'commission_amount' => $request->commission_amount ?? 0,
                    'discount_percent' => $request->discount_percent ?? 0,
                    'discount_amount' => $request->discount_amount ?? 0,
                ]
            );
        }

        notyf()->success('Agent updated successfully!');

        if ($request->input('action') === 'save_stay') {
            return redirect()->back();
        } else {
            return redirect()->route('admin.agent.index');
        }

    }

    public function destroy(string $id)
    {
         $agent = User::findOrFail($id);

        if ($agent->commissions()->exists()) {
            return response(['status' => 'error', 'message' => 'You cannot delete an agent who has commissions!'], 500);
        }

        // Block deletion if agent has documents
        if ($agent->documents()->exists()) {
            return response(['status' => 'error', 'message' => 'You cannot delete an agent who has documents!'], 500);
        }

        try {
            $agent->delete();
            return response(['status' => 'success', 'message' => 'Agent deleted successfully!'], 200);
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
