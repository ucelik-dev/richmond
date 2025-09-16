<?php

namespace App\Http\Controllers\Frontend\Agent;

use App\Http\Controllers\Controller;
use App\Http\Requests\Agent\AgentPasswordUpdateRequest;
use App\Http\Requests\Agent\AgentProfileUpdateRequest;
use App\Http\Requests\Agent\AgentSocialUpdateRequest;
use App\Models\Country;
use App\Models\SocialPlatform;
use App\Models\User;
use App\Traits\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgentProfileController extends Controller
{
    use FileUpload;
    
    function index() {
        $agent = User::with('socialAccounts')->findOrFail(Auth::user()->id);
        $socialPlatforms = SocialPlatform::where('status', 1)->get();
        $agentSocialAccounts = $agent->socialAccounts->keyBy('social_platform_id'); 
        $countries = Country::where('status', 1)->get();
        
        return view ('frontend.agent.profile.index', compact('countries','agent','socialPlatforms','agentSocialAccounts'));
    }

    function updateProfile(AgentProfileUpdateRequest $request) {
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

        $user->save();

        
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

    function updatePassword(AgentPasswordUpdateRequest $request) {
        
        $user = Auth::user();
        $user->password = bcrypt($request->password);
        $user->save();

        notyf()->success('Password updated successfully!');
        return redirect()->back();
    }

    function updateSocial(AgentSocialUpdateRequest $request) {
        $user = Auth::user();
        $submittedPlatformIds = [];

        if ($request->has('socialPlatforms')) {
            foreach ($request->socialPlatforms as $platformInput) {
                $platformId = $platformInput['social_platform_id'] ?? null;
                $link = trim($platformInput['link'] ?? '');

                if (!$platformId) continue;

                $submittedPlatformIds[] = $platformId;

                // Check if the user already has this platform
                $existing = $user->socialAccounts()
                    ->where('social_platform_id', $platformId)
                    ->first();

                if ($existing) {
                    if ($link === '') {
                        // ✅ Delete if link is empty
                        $existing->delete();
                    } else {
                        // ✅ Update if changed
                        $existing->update([
                            'link' => $link,
                        ]);
                    }
                } elseif ($link !== '') {
                    // ✅ Create new
                    $user->socialAccounts()->create([
                        'social_platform_id' => $platformId,
                        'link' => $link,
                    ]);
                }
            }

            // ✅ Remove any existing that were not submitted at all
            $user->socialAccounts()
                ->whereNotIn('social_platform_id', $submittedPlatformIds)
                ->delete();
        }

        notyf()->success('Social media links updated successfully!');
        return redirect()->back();

    }

}
