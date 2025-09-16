<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        $roles = Role::where('status',1)->get();
        return view('auth.register', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:male,female,other'],
            'role' => ['required', 'numeric'],
            'phone' => ['required', 'string', 'max:20'], 
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'dob' => ['required', 'date'], 
            'post_code' => ['required', 'string', 'max:20'],
            'city' => ['required', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

     
        $user = User::create([
            'name' => $request->name,
            'gender' => $request->gender,
            'phone' => $request->phone,
            'email' => $request->email,
            'dob' => $request->dob,
            'post_code' => $request->post_code,
            'city' => $request->city,
            'country' => $request->country,
            'address' => $request->address,
            'password' => Hash::make($request->password),
            'approve_status' => 'pending',
        ]);

        // Attach the role to the user and set 'is_main' to true for this role
        $user->roles()->attach($request->role, ['is_main' => true]);
       
        event(new Registered($user));

        Auth::login($user);

        $mainRole = $user->main_role?->name; // Use the accessor $user->main_role
        if ($mainRole && \Route::has($mainRole . '.dashboard')) {
            return redirect()->intended(route($mainRole . '.dashboard'));
        }

        return abort(404);

    }
}
