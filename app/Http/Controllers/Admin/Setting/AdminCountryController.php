<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class AdminCountryController extends Controller
{
    
    public function index()
    {
        $countries = Country::all();
        return view('admin.setting.country.index', compact('countries'));
    }

    public function create()
    {
        return view('admin.setting.country.create');
    }

    public function store(Request $request)
    {
        // Validate form inputs
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10',
            'status' => 'required|in:0,1',
        ]);

        // Create the awarding body
        Country::create([
            'name' => $request->name,
            'code' => $request->code,
            'status' => $request->status,
        ]);

        notyf()->success('Created successfully!');
        return redirect()->route('admin.setting-country.index');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $country = Country::findOrFail($id);
        return view('admin.setting.country.edit', compact('country'));
    }

    public function update(Request $request, string $id)
    {
        // Validate form inputs
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10',
            'status' => 'required|in:0,1', 
        ]);

        // Find and update the record
        $country = Country::findOrFail($id);

        $country->update([
            'name' => $request->name,
            'code' => $request->code,
            'status' => $request->status,
        ]);

        notyf()->success('Updated successfully!');
        return redirect()->route('admin.setting-country.index');
    }

    public function destroy(string $id)
    {
        try {
            $country = Country::findOrFail($id);
            $country->delete();
            notyf()->success('Deleted successfully!');
            return response(['status' => 'success', 'message' => 'Deleted successfully!'], 200);
        } catch (\Exception $e) {
            return response(['status' => 'error', 'message' => 'Something went wrong!'], 500);
        }
    }

}
