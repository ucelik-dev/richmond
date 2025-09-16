<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use Illuminate\Http\Request;

class AdminStudentBatchController extends Controller
{
    public function index()
    {
        $batches = Batch::all();
        return view('admin.setting.student-batch.index', compact('batches'));
    }

    public function create()
    {
        return view('admin.setting.student-batch.create');
    }

    public function store(Request $request)
    {
        // Validate form inputs
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'start_date' => 'nullable|date', 
            'status' => 'required|in:0,1',
        ]);

        // Create the awarding body
        Batch::create([
            'name' => $request->name,
            'color' => $request->color,
            'start_date' => $request->start_date,
            'status' => $request->status,
        ]);

        notyf()->success('Created successfully!');
        return redirect()->route('admin.setting-student-batch.index');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $batch = Batch::findOrFail($id);
        return view('admin.setting.student-batch.edit', compact('batch'));
    }

    public function update(Request $request, string $id)
    {
        // Validate form inputs
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'start_date' => 'nullable|date', 
            'status' => 'required|in:0,1', 
        ]);

        // Find and update the record
        $batch = Batch::findOrFail($id);

        $batch->update([
            'name' => $request->name,
            'color' => $request->color,
            'start_date' => $request->start_date,
            'status' => $request->status,
        ]);

        notyf()->success('Updated successfully!');
        return redirect()->route('admin.setting-student-batch.index');
    }

    public function destroy(string $id)
    {
        try {
            $batch = Batch::findOrFail($id);
            $batch->delete();
            notyf()->success('Deleted successfully!');
            return response(['status' => 'success', 'message' => 'Deleted successfully!'], 200);
        } catch (\Exception $e) {
            return response(['status' => 'error', 'message' => 'Something went wrong!'], 500);
        }
    }

}
