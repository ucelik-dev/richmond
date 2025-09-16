<?php

namespace App\Http\Controllers\Frontend\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\Student\StudentPasswordUpdateRequest;
use App\Http\Requests\Frontend\Student\StudentProfileUpdateRequest;
use App\Http\Requests\Frontend\Student\StudentSocialUpdateRequest;
use App\Models\Country;
use App\Models\SocialPlatform;
use App\Models\User;
use App\Traits\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentProfileController extends Controller
{
    use FileUpload;
    
    function index() {
        $student = User::with('socialAccounts')->findOrFail(Auth::user()->id);
        $socialPlatforms = SocialPlatform::where('status', 1)->get();
        $studentSocialAccounts = $student->socialAccounts->keyBy('social_platform_id'); 
        $countries = Country::where('status', 1)->get();

        return view ('frontend.student.profile.index', compact('countries','student','socialPlatforms','studentSocialAccounts'));
    }

    function updateProfile(StudentProfileUpdateRequest $request) {
        $user = Auth::user(); // get the currently logged-in user

        if($request->hasFile('avatar')){
            $avatarPath = $this->uploadFile($request->file('avatar'), 'uploads/profile-images', 'image');
            $this->deleteFile($user->image);
            $user->image = $avatarPath;
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
        $user->update();

        notyf()->success('Profile updated successfully!');
        return redirect()->back();
    }

    function updateAvatar(Request $request) {

        $request->validate([
            'avatar' => ['nullable', 'image', 'max:5000'],
        ]);

        $user = Auth::user();

        if($request->hasFile('avatar')){
            $avatarPath = $this->uploadFile($request->file('avatar'), 'uploads/profile-images', 'image');
            $this->deleteFile($user->image);
            $user->image = $avatarPath;
            $user->save();
        }

        notyf()->success('Profile picture updated successfully!');
        return redirect()->back();
    }

    function updatePassword(StudentPasswordUpdateRequest $request) {
        
        $user = Auth::user();
        $user->password = bcrypt($request->password);
        $user->save();
        
        notyf()->success('Password updated successfully!');
        return redirect()->back();
    }

}
