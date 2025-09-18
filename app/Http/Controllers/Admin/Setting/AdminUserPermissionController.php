<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Str;

class AdminUserPermissionController extends Controller
{
    
    public function index()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::with('roles')->get();
        return view('admin.setting.user-permission.index', compact('permissions','roles'));
    }

    public function create()
    {
        $roles = Role::all();
        $permissions = Permission::all();

        return view('admin.setting.user-permission.create', compact('permissions', 'roles'));
    }

    public function store(Request $request)
    {
        // 1. Create the new permission
        $permission = Permission::create([
            'name' => $request->name,
            'display_name' => $request->display_name, // Automatically create a slug
            'description' => $request->description,
        ]);

        // 2. Attach the permission to the selected roles
        if ($request->has('roles') && is_array($request->roles)) {
            $permission->roles()->sync($request->roles);
        }

        notyf()->success('Permission created and assigned successfully!');
        return redirect()->route('admin.setting-user-permission.index');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $roles = Role::all();
        $permission = Permission::findOrFail($id);
        // Get the IDs of the roles this permission is already attached to
        $permissionRoles = $permission->roles->pluck('id')->toArray();

        return view('admin.setting.user-permission.edit', compact('permission', 'roles', 'permissionRoles'));
    }

    public function update(Request $request, string $id)
    {
        // Validate form inputs
        $request->validate([
            'name' => 'required|string|max:255',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255', 
        ]);

        // Find and update the record
        $permission = Permission::findOrFail($id);

        $permission->update([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->description,
        ]);
        
        // Update role attachments
        if ($request->has('roles') && is_array($request->roles)) {
            $permission->roles()->sync($request->roles);
        } else {
            // If no roles are selected, detach all roles
            $permission->roles()->detach();
        }

        notyf()->success('Permission updated successfully!');
        return redirect()->route('admin.setting-user-permission.index');
    }

    public function destroy(Permission $permission)
    {
        // Detach all roles before deleting the permission
        $permission->roles()->detach();
        $permission->delete();

        notyf()->success('Permission deleted successfully!');
        return redirect()->route('admin.setting-user-permission.index');
    }

}
