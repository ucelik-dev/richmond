<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use App\Models\DocumentCategory;
use App\Models\Role;
use Illuminate\Http\Request;

class AdminDocumentCategoryController extends Controller
{
    
    public function index()
    {
        $documentCategories = DocumentCategory::with('role')->get();
        return view('admin.setting.document-category.index', compact('documentCategories'));
    }

    public function create()
    {
        $roles = Role::where('status', 1)->get();
        return view('admin.setting.document-category.create', compact('roles'));
    }

    public function store(Request $request)
    {
        // Validate form inputs
        $request->validate([
            'name' => 'required|string|max:255',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:0,1',
        ]);

        // Create the awarding body
        DocumentCategory::create([
            'name' => $request->name,
            'role_id' => $request->role_id,
            'status' => $request->status,
        ]);

        notyf()->success('Created successfully!');
        return redirect()->route('admin.setting-document-category.index');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $documentCategory = DocumentCategory::findOrFail($id);
        $roles = Role::where('status', 1)->get();
        return view('admin.setting.document-category.edit', compact('documentCategory','roles'));
    }

    public function update(Request $request, string $id)
    {
        // Validate form inputs
        $request->validate([
            'name' => 'required|string|max:255',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:0,1', 
        ]);

        // Find and update the record
        $documentCategory = DocumentCategory::findOrFail($id);

        $documentCategory->update([
            'name' => $request->name,
            'role_id' => $request->role_id,
            'status' => $request->status,
        ]);

        notyf()->success('Updated successfully!');
        return redirect()->route('admin.setting-document-category.index');
    }

    public function destroy(string $id)
    {
        try {
            $documentCategory = DocumentCategory::findOrFail($id);
            $documentCategory->delete();
            notyf()->success('Deleted successfully!');
            return response(['status' => 'success', 'message' => 'Deleted successfully!'], 200);
        } catch (\Exception $e) {
            return response(['status' => 'error', 'message' => 'Something went wrong!'], 500);
        }
    }

}
