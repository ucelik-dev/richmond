<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use App\Models\SocialPlatform;
use Illuminate\Http\Request;

class AdminSocialPlatformController extends Controller
{
    
    public function index()
    {
        $socialPlatforms = SocialPlatform::all();
        return view('admin.setting.social-platform.index', compact('socialPlatforms'));
    }

    public function create()
    {
        return view('admin.setting.social-platform.create');
    }

    public function store(Request $request)
    {
        // Validate form inputs
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:0,1',
        ]);

        // Create the awarding body
        SocialPlatform::create([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        notyf()->success('Created successfully!');
        return redirect()->route('admin.setting-social-platform.index');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $socialPlatform = SocialPlatform::findOrFail($id);
        return view('admin.setting.social-platform.edit', compact('socialPlatform'));
    }

    public function update(Request $request, string $id)
    {
        // Validate form inputs
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:0,1', 
        ]);

        // Find and update the record
        $socialPlatform = SocialPlatform::findOrFail($id);

        $socialPlatform->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        notyf()->success('Updated successfully!');
        return redirect()->route('admin.setting-social-platform.index');
    }

    public function destroy(string $id)
    {
        try {
            $socialPlatform = SocialPlatform::findOrFail($id);
            $socialPlatform->delete();
            notyf()->success('Deleted successfully!');
            return response(['status' => 'success', 'message' => 'Deleted successfully!'], 200);
        } catch (\Exception $e) {
            return response(['status' => 'error', 'message' => 'Something went wrong!'], 500);
        }
    }

}
