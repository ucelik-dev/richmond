<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;

class AdminStudentGroupController extends Controller
{
    
    public function index()
    {
        $groups = Group::with('instructor')->get();
        return view('admin.setting.student-group.index', compact('groups'));
    }

    public function create()
    {
        $instructors = User::whereHas('mainRoleRelation', fn ($q) => $q->where('name', 'instructor'))
        ->orderBy('name', 'ASC')
        ->get();

        return view('admin.setting.student-group.create', compact('instructors'));
    }

    public function store(Request $request)
    {
        // Validate form inputs
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'instructor_id' => 'nullable|exists:users,id',
            'status' => 'required|in:0,1',
        ]);

        // Create the awarding body
        Group::create([
            'name' => $request->name,
            'color' => $request->color,
            'instructor_id' => $request->instructor_id,
            'status' => $request->status,
        ]);

        notyf()->success('Created successfully!');
        return redirect()->route('admin.setting-student-group.index');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $group = Group::findOrFail($id);
        $instructors = User::whereHas('mainRoleRelation', fn ($q) => $q->where('name', 'instructor'))
        ->orderBy('name', 'ASC')
        ->get();
        return view('admin.setting.student-group.edit', compact('group','instructors'));
    }

    public function update(Request $request, string $id)
    {
        // Validate form inputs
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'instructor_id' => 'nullable|exists:users,id',
            'status' => 'required|in:0,1', 
        ]);

        // Find and update the record
        $group = Group::findOrFail($id);

        $group->update([
            'name' => $request->name,
            'color' => $request->color,
            'instructor_id' => $request->instructor_id,
            'status' => $request->status,
        ]);

        notyf()->success('Updated successfully!');
        return redirect()->route('admin.setting-student-group.index');
    }

    public function destroy(string $id)
    {
        try {
            $group = Group::findOrFail($id);
            $group->delete();
            notyf()->success('Deleted successfully!');
            return response(['status' => 'success', 'message' => 'Deleted successfully!'], 200);
        } catch (\Exception $e) {
            return response(['status' => 'error', 'message' => 'Something went wrong!'], 500);
        }
    }

}
