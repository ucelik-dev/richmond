<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use App\Models\RecruitmentStatus;
use Illuminate\Http\Request;

class AdminRecruitmentStatusController extends Controller
{

    public function index()
    {
        $recruitmentStatuses = RecruitmentStatus::all();
        return view('admin.setting.recruitment-status.index', compact('recruitmentStatuses'));
    }

    public function create()
    {
        return view('admin.setting.recruitment-status.create');
    }

    public function store(Request $request)
    {
        // Validate form inputs
        $request->validate([
            'name' => 'required|string|max:255',
            'label' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        RecruitmentStatus::create([
            'name' => $request->name,
            'label' => $request->label,
            'color' => $request->color,
            'status' => $request->status,
        ]);

        notyf()->success('Created successfully!');
        return redirect()->route('admin.setting-recruitment-status.index');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $recruitmentStatus = RecruitmentStatus::findOrFail($id);
        return view('admin.setting.recruitment-status.edit', compact('recruitmentStatus'));
    }

    public function update(Request $request, string $id)
    {
        // Validate form inputs
        $request->validate([
            'name' => 'required|string|max:255',
            'label' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        // Find and update the record
        $recruitmentStatus = RecruitmentStatus::findOrFail($id);

        $recruitmentStatus->update([
            'name' => $request->name,
            'label' => $request->label,
            'color' => $request->color,
            'status' => $request->status,
        ]);

        notyf()->success('Updated successfully!');
        return redirect()->route('admin.setting-recruitment-status.index');
    }

    public function destroy(string $id)
    {
        try {
            $recruitmentStatus = RecruitmentStatus::findOrFail($id);
            $recruitmentStatus->delete();
            notyf()->success('Deleted successfully!');
            return response(['status' => 'success', 'message' => 'Deleted successfully!'], 200);
        } catch (\Exception $e) {
            return response(['status' => 'error', 'message' => 'Something went wrong!'], 500);
        }
    }

}
