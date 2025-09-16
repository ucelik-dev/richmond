<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use App\Models\AwardingBody;
use Illuminate\Http\Request;

class AdminAwardingBodyController extends Controller
{

    public function index()
    {
        $awardingBodies = AwardingBody::all();
        return view('admin.setting.awarding-body.index', compact('awardingBodies'));
    }

    public function create()
    {
        return view('admin.setting.awarding-body.create');
    }

    public function store(Request $request)
    {
        // Validate form inputs
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:0,1',
        ]);

        // Create the awarding body
        AwardingBody::create([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        notyf()->success('Created successfully!');
        return redirect()->route('admin.setting-awarding-body.index');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $awardingBody = AwardingBody::findOrFail($id);
        return view('admin.setting.awarding-body.edit', compact('awardingBody'));
    }

    public function update(Request $request, string $id)
    {
        // Validate form inputs
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:0,1', 
        ]);

        // Find and update the record
        $awardingBody = AwardingBody::findOrFail($id);

        $awardingBody->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        notyf()->success('Updated successfully!');
        return redirect()->route('admin.setting-awarding-body.index');
    }

    public function destroy(string $id)
    {
        try {
            $awardingBody = AwardingBody::findOrFail($id);
            $awardingBody->delete();
            notyf()->success('Deleted successfully!');
            return response(['status' => 'success', 'message' => 'Deleted successfully!'], 200);
        } catch (\Exception $e) {
            return response(['status' => 'error', 'message' => 'Something went wrong!'], 500);
        }
    }

}
