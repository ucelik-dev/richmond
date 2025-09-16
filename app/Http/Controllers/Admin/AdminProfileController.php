<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminProfileUpdateRequest;
use App\Models\Country;
use App\Models\DocumentCategory;
use App\Traits\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminProfileController extends Controller
{
    use FileUpload;

    public function edit()
    {
        $user = Auth::user();
        $countries = Country::orderBy('name')->get();
        $documentCategories = DocumentCategory::orderBy('name')->get();

        return view('admin.profile.edit', compact(
            'user','countries','documentCategories'
        ));
    }


    function update(AdminProfileUpdateRequest $request) {
        $user = Auth::user(); 

        if ($request->hasFile('image')) {
            $imagePath = $this->uploadFile($request->file('image'), 'uploads/profile-images', 'image');
            // delete old image if you store relative paths
            if (!empty($user->image)) {
                $this->deleteFile($user->image);
            }
            $user->image = $imagePath;
        }

        $user->name = $request->name;
        $user->gender = $request->gender;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->dob = $request->dob;
        $user->post_code = $request->post_code;
        $user->city = $request->city;
        $user->country_id = $request->country_id;
        $user->address = $request->address;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->update();

        if ($request->input('action') === 'save_stay') {
            return redirect()->back();
        } else {
            return redirect()->route('admin.dashboard');
        }
    }

   

}
