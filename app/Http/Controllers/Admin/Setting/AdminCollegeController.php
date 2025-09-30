<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Traits\FileUpload;
use Illuminate\Http\Request;

class AdminCollegeController extends Controller
{
    use FileUpload;

    public function index()
    {
        $colleges = College::all();
        return view('admin.setting.college.index', compact('colleges'));
    }

    public function create()
    {
        return view('admin.setting.college.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5000'],
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|string|lowercase|email|max:255',
            'bank_account' => 'nullable|string|max:5000',
            'invoice_data' => 'nullable|string|max:5000',
            'status' => 'required|in:0,1',
        ]);
        
        $college = new College();

        // Upload profile image if present
        if ($request->hasFile('logo')) {
            $logoPath = $this->uploadFile($request->file('logo'), 'uploads/profile-images', 'logo');
            $college->logo = $logoPath;
        }

        $college->name = $request->name;
        $college->code = $request->code;
        $college->url = $request->url;
        $college->phone = $request->phone;
        $college->email = $request->email;
        $college->bank_account = $request->bank_account;
        $college->invoice_data = $request->invoice_data;
        $college->status = $request->status;
        $college->save();

        notyf()->success('College created successfully!');
        return redirect()->route('admin.setting-college.index');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $college = College::findOrFail($id);
        return view('admin.setting.college.edit', compact('college'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5000'],
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|string|lowercase|email|max:255',
            'bank_account' => 'nullable|string|max:5000',
            'invoice_data' => 'nullable|string|max:5000',
            'status' => 'required|in:0,1',
        ]);

        $college = College::findOrFail($id);

        if ($request->hasFile('logo')) {
            $logoPath = $this->uploadFile($request->file('logo'), 'uploads/profile-images', 'logo');
            $this->deleteFile($college->logo);
            $college->logo = $logoPath;
        }

        $college->update([
            'name' => $request->name,
            'code' => $request->code,
            'url' => $request->url,
            'phone' => $request->phone,
            'email' => $request->email,
            'bank_account' => $request->bank_account,
            'invoice_data' => $request->invoice_data,
            'status' => $request->status
        ]);

        notyf()->success('Updated successfully!');
        return redirect()->route('admin.setting-college.index');
    }

    public function destroy(string $id)
    {
        try {
            $college = College::findOrFail($id);
            $this->deleteFile($college->logo);
            $college->delete();
            notyf()->success('Deleted successfully!');
            return response(['status' => 'success', 'message' => 'Deleted successfully!'], 200);
        } catch (\Exception $e) {
            return response(['status' => 'error', 'message' => 'Something went wrong!'], 500);
        }
    }

}
