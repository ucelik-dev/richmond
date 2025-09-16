<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use App\Models\RecruitmentSource;
use Illuminate\Http\Request;

class AdminRecruitmentSourceController extends Controller
{
    
    public function index()
    {
        $recruitmentSources = RecruitmentSource::all();
        return view('admin.setting.recruitment-source.index', compact('recruitmentSources'));
    }

    public function create()
    {
        return view('admin.setting.recruitment-source.create');
    }

    public function store(Request $request)
    {
        // Validate form inputs
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        RecruitmentSource::create([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        notyf()->success('Created successfully!');
        return redirect()->route('admin.setting-recruitment-source.index');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $recruitmentSource = RecruitmentSource::findOrFail($id);
        return view('admin.setting.recruitment-source.edit', compact('recruitmentSource'));
    }

    public function update(Request $request, string $id)
    {
        // Validate form inputs
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        // Find and update the record
        $recruitmentSource = RecruitmentSource::findOrFail($id);

        $recruitmentSource->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        notyf()->success('Updated successfully!');
        return redirect()->route('admin.setting-recruitment-source.index');
    }

    public function destroy(string $id)
    {
        try {
            $recruitmentSource = RecruitmentSource::findOrFail($id);
            $recruitmentSource->delete();
            notyf()->success('Deleted successfully!');
            return response(['status' => 'success', 'message' => 'Deleted successfully!'], 200);
        } catch (\Exception $e) {
            return response(['status' => 'error', 'message' => 'Something went wrong!'], 500);
        }
    }

}
