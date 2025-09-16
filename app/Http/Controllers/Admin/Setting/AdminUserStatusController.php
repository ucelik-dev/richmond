<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use App\Models\UserStatus;
use Illuminate\Http\Request;

class AdminUserStatusController extends Controller
{
    
    public function index()
    {
        $userStatuses = UserStatus::all();
        return view('admin.setting.user-status.index', compact('userStatuses'));
    }

    public function create()
    {
        return view('admin.setting.user-status.create');
    }

    public function store(Request $request)
    {
        // Validate form inputs
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'status' => 'required|in:0,1',
            'order' => 'required|integer|unique:user_statuses,order', 
        ]);

        // Create the awarding body
        UserStatus::create([
            'name' => $request->name,
            'color' => $request->color,
            'status' => $request->status,
            'order' => $request->order,
        ]);

        notyf()->success('Created successfully!');
        return redirect()->route('admin.setting-user-status.index');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $userStatus = UserStatus::findOrFail($id);
        return view('admin.setting.user-status.edit', compact('userStatus'));
    }

    public function update(Request $request, string $id)
    {
        // Validate form inputs
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'status' => 'required|in:0,1', 
            'order' => 'required|integer|unique:user_statuses,order', 
        ]);

        // Find and update the record
        $userStatus = UserStatus::findOrFail($id);

        $userStatus->update([
            'name' => $request->name,
            'color' => $request->color,
            'status' => $request->status,
            'order' => $request->order,
        ]);

        notyf()->success('Updated successfully!');
        return redirect()->route('admin.setting-user-status.index');
    }

    public function destroy(string $id)
    {
        try {
            $userStatus = UserStatus::findOrFail($id);
            $userStatus->delete();
            notyf()->success('Deleted successfully!');
            return response(['status' => 'success', 'message' => 'Deleted successfully!'], 200);
        } catch (\Exception $e) {
            return response(['status' => 'error', 'message' => 'Something went wrong!'], 500);
        }
    }

}
