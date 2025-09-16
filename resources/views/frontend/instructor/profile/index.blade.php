@extends('frontend.layouts.master')

@section('content')

    <!-- DASHBOARD OVERVIEW START -->
    <section class="wsus__dashboard mt_150 pb_50">
        <div class="container-fluid px-4">
            <div class="row">

                @include('frontend.instructor.sidebar')

                <div class="col-xl-10 col-md-8 wow fadeInRight" style="visibility: visible; animation-name: fadeInRight;">

                    <div class="wsus__dashboard_content">
                        <div class="wsus__dashboard_content_top d-flex flex-wrap justify-content-between">
                            <div class="wsus__dashboard_heading">
                                <h5>Update Your Information</h5>
                                <p>Manage your courses and its update like live, draft and insight.</p>
                            </div>
                        </div>

                        <form action="{{ route('instructor.profile.update') }}" method="POST"  class="wsus__dashboard_profile_update">
                            @csrf

                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="wsus__login_form_input">
                                        <label class="label-required">Name</label>
                                        <input type="text" name="name" placeholder="Enter your name" value="{{ auth()->user()->name }}">
                                        <x-input-error :messages="$errors->get('name')" class="mt-2 text-danger small" />
                                    </div>
                                </div>

                                <div class="col-xl-6">
                                    <div class="wsus__login_form_input">
                                        <label class="label-required">Gender</label>
                                        <select name="gender" id="" class="form-control form-select p-2">
                                            <option value="">Select</option>
                                            <option @selected(auth()->user()->gender === 'male') value="male">Male</option>
                                            <option @selected(auth()->user()->gender === 'female') value="female">Female</option>
                                            <option @selected(auth()->user()->gender === 'other') value="other">Other</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('gender')" class="mt-2 text-danger small" />
                                    </div>
                                </div>

                                <div class="col-xl-6">
                                    <div class="wsus__login_form_input">
                                        <label class="label-required">Phone</label>
                                        <input type="text"name="phone" placeholder="Enter your number" value="{{ auth()->user()->phone }}">
                                        <x-input-error :messages="$errors->get('phone')" class="mt-2 text-danger small" />
                                    </div>
                                </div>

                                <div class="col-xl-6">
                                    <div class="wsus__login_form_input">
                                        <label class="label-required">Email</label>
                                        <input type="email" name="email" placeholder="Enter your mail" value="{{ auth()->user()->email }}">
                                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-danger small" />
                                    </div>
                                </div>

                                <div class="col-xl-6">
                                    <div class="wsus__login_form_input">
                                        <label class="label-required">Birthdate</label>
                                        <input type="date" name="dob" placeholder="Enter birthdate" value="{{ auth()->user()->dob }}">
                                        <x-input-error :messages="$errors->get('dob')" class="mt-2 text-danger small" />
                                    </div>
                                </div>

                                <div class="col-xl-6">
                                    <div class="wsus__login_form_input">
                                        <label class="label-required">Post Code</label>
                                        <input type="text" name="post_code" placeholder="Enter zip code" value="{{ auth()->user()->post_code }}">
                                        <x-input-error :messages="$errors->get('post_code')" class="mt-2 text-danger small" />
                                    </div>
                                </div>

                                <div class="col-xl-6">
                                    <div class="wsus__login_form_input">
                                        <label class="label-required">City</label>
                                        <input type="text" name="city" placeholder="Enter your city" value="{{ auth()->user()->city }}">
                                        <x-input-error :messages="$errors->get('city')" class="mt-2 text-danger small" />
                                    </div>
                                </div>

                                <div class="col-xl-6">
                                    <div class="wsus__login_form_input">
                                        <label class="label-required">Country</label>
                                        <select name="country_id" class="form-control form-select">
                                            <option value="">Select a country</option>
                                            @foreach($countries as $country)
                                                <option @selected(auth()->user()->country->id === $country->id) value="{{ $country->id }}">{{ $country->name }}</option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('country_id')" class="mt-2 text-danger small" />
                                    </div>
                                </div>

                                <div class="col-xl-12">
                                    <div class="wsus__login_form_input">
                                        <label class="label-required">Address</label>
                                        <textarea name="address" rows="3" placeholder="Enter your address">{{ auth()->user()->address }}</textarea>
                                        <x-input-error :messages="$errors->get('address')" class="mt-2 text-danger small" />
                                    </div>
                                </div>

                                <div class="col-xl-12">
                                    <div class="wsus__login_form_input">
                                        <label>About Me</label>
                                        <textarea name="bio" rows="7" placeholder="Your text here">{{ auth()->user()->bio }}</textarea>
                                        <x-input-error :messages="$errors->get('bio')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                @can('edit_instructor_profile')
                                    <div class="col-xl-12">
                                        <div class="wsus__dashboard_profile_update_btn">
                                            <button type="submit" class="common_btn">Update Profile</button>
                                        </div>
                                    </div>
                                @endcan
                            </div>
                        </form>
                    </div>

                    <div class="wsus__dashboard_content">
                        <div class="wsus__dashboard_content_top d-flex flex-wrap justify-content-between">
                            <div class="wsus__dashboard_heading">
                                <h5>Update Your Password</h5>
                                <p>Set a new password for your account.</p>
                            </div>
                        </div>

                        <form action="{{ route('instructor.profile.update-password') }}" method="POST"
                            class="wsus__dashboard_profile_update">
                            @csrf

                            <div class="row">

                                <div class="col-lg-4">
                                    <div class="wsus__login_form_input">
                                        <label class="label-required">Current password</label>
                                        <input type="password" name="current_password" placeholder="Enter your current password">
                                        <x-input-error :messages="$errors->get('current_password')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="wsus__login_form_input">
                                        <label class="label-required">New password</label>
                                        <input type="password" name="password" placeholder="Enter your new password">
                                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="wsus__login_form_input">
                                        <label class="label-required">Confirm new password</label>
                                        <input type="password" name="password_confirmation" placeholder="Confirm your new password">
                                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-danger small" />
                                    </div>
                                </div>

                                @can('edit_instructor_profile')
                                    <div class="col-xl-12">
                                        <div class="wsus__dashboard_profile_update_btn">
                                            <button type="submit" class="common_btn">Update Password</button>
                                        </div>
                                    </div>
                                @endcan

                            </div>
                        </form>
                    </div>

                    <div class="wsus__dashboard_content">
                        <div class="wsus__dashboard_content_top d-flex flex-wrap justify-content-between">
                            <div class="wsus__dashboard_heading">
                                <h5>Update Your Social Media Information</h5>
                                <p>Put your social links here.</p>
                            </div>
                        </div>

                        <form action="{{ route('instructor.profile.update-social') }}" method="POST" class="wsus__dashboard_profile_update">
                            @csrf

                             {{-- Edit social accounts --}}

                            <div class="row">
                                <div class="col-xl-12 table-responsive">
                                    <table class="table table-bordered" id="socialAccountsTable">
                                        <thead>
                                            <tr>
                                                <th class="text-nowrap">Social Platform</th>
                                                <th class="text-nowrap">Link</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @foreach ($socialPlatforms as $index => $socialPlatform)
                                            @php
                                                $savedAccount = $instructorSocialAccounts[$socialPlatform->id] ?? null;
                                            @endphp
                                                <tr>
                                                    <td class="align-middle">
                                                        <input type="hidden" name="socialPlatforms[{{ $index }}][social_platform_id]" value="{{ $socialPlatform->id }}">
                                                        <input type="text" class="form-control" value="{{ $socialPlatform->name }}" disabled>
                                                    </td>
                                                    <td>
                                                        <input type="text" 
                                                            name="socialPlatforms[{{ $index }}][link]" 
                                                            class="form-control" 
                                                            value="{{ old("socialPlatforms.{$index}.link", $savedAccount->link ?? '') }}">

                                                        @if ($savedAccount)
                                                            <input type="hidden" name="socialPlatforms[{{ $index }}][id]" value="{{ $savedAccount->id }}">
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
                                    
                                </div>

                                @can('edit_instructor_profile')
                                    <div class="col-xl-12">
                                        <div class="wsus__dashboard_profile_update_btn">
                                            <button type="submit" class="common_btn">Update Social Links</button>
                                        </div>
                                    </div>
                                @endcan
                                
                            </div>


                        </form>
                    </div>

                </div>

            </div>
        </div>
    </section>
    <!-- DASHBOARD OVERVIEW END -->
@endsection
