<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use App\Models\CourseLevel;
use Illuminate\Http\Request;

class AdminCourseLevelController extends Controller
{
    
    public function index()
    {
        $courseLevels = CourseLevel::all();
        return view('admin.setting.course-level.index', compact('courseLevels'));
    }

    public function create()
    {
        return view('admin.setting.course-level.create');
    }

    public function store(Request $request)
    {
        // Validate form inputs
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:0,1',
        ]);

        // Create the awarding body
        CourseLevel::create([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        notyf()->success('Created successfully!');
        return redirect()->route('admin.setting-course-level.index');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $courseLevel = CourseLevel::findOrFail($id);
        return view('admin.setting.course-level.edit', compact('courseLevel'));
    }

    public function update(Request $request, string $id)
    {
        // Validate form inputs
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:0,1', 
        ]);

        // Find and update the record
        $courseLevel = CourseLevel::findOrFail($id);

        $courseLevel->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        notyf()->success('Updated successfully!');
        return redirect()->route('admin.setting-course-level.index');
    }

    public function destroy(string $id)
    {
        try {
            $courseLevel = CourseLevel::findOrFail($id);
            $courseLevel->delete();
            notyf()->success('Deleted successfully!');
            return response(['status' => 'success', 'message' => 'Deleted successfully!'], 200);
        } catch (\Exception $e) {
            return response(['status' => 'error', 'message' => 'Something went wrong!'], 500);
        }
    }

}
