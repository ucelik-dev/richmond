<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class AdminUserRoleController extends Controller
{
    
    public function index()
    {
        $roles = Role::all();
        return view('admin.setting.user-role.index', compact('roles'));
    }

    public function create()
    {
        return view('admin.setting.user-role.create');
    }

    public function store(Request $request)
    {
        // Validate form inputs
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'status' => 'required|in:0,1',
        ]);

        // Create the awarding body
        Role::create([
            'name' => $request->name,
            'color' => $request->color,
            'status' => $request->status,
        ]);

        notyf()->success('Created successfully!');
        return redirect()->route('admin.setting-user-role.index');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $role = Role::findOrFail($id);
        return view('admin.setting.user-role.edit', compact('role'));
    }

    public function update(Request $request, string $id)
    {
        // Validate form inputs
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'status' => 'required|in:0,1', 
        ]);

        // Find and update the record
        $role = Role::findOrFail($id);

        $role->update([
            'name' => $request->name,
            'color' => $request->color,
            'status' => $request->status,
        ]);

        notyf()->success('Updated successfully!');
        return redirect()->route('admin.setting-user-role.index');
    }

    public function destroy(string $id)
    {
        try {
            $role = Role::findOrFail($id);
            $role->delete();
            notyf()->success('Deleted successfully!');
            return response(['status' => 'success', 'message' => 'Deleted successfully!'], 200);
        } catch (\Exception $e) {
            return response(['status' => 'error', 'message' => 'Something went wrong!'], 500);
        }
    }

}
