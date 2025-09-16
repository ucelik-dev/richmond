<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use App\Models\CourseCategory;
use Illuminate\Http\Request;

class AdminCourseCategoryController extends Controller
{
    
    public function index()
    {
        $courseCategories = CourseCategory::all();
        return view('admin.setting.course-category.index', compact('courseCategories'));
    }

    public function create()
    {
        return view('admin.setting.course-category.create');
    }

    public function store(Request $request)
    {
        // Validate form inputs
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:0,1',
        ]);

        // Create the awarding body
        CourseCategory::create([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        notyf()->success('Created successfully!');
        return redirect()->route('admin.setting-course-category.index');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $courseCategory = CourseCategory::findOrFail($id);
        return view('admin.setting.course-category.edit', compact('courseCategory'));
    }

    public function update(Request $request, string $id)
    {
        // Validate form inputs
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:0,1', 
        ]);

        // Find and update the record
        $courseCategory = CourseCategory::findOrFail($id);

        $courseCategory->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        notyf()->success('Updated successfully!');
        return redirect()->route('admin.setting-course-category.index');
    }

    public function destroy(string $id)
    {
        try {
            $courseCategory = CourseCategory::findOrFail($id);
            $courseCategory->delete();
            notyf()->success('Deleted successfully!');
            return response(['status' => 'success', 'message' => 'Deleted successfully!'], 200);
        } catch (\Exception $e) {
            return response(['status' => 'error', 'message' => 'Something went wrong!'], 500);
        }
    }

}
