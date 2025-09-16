@extends('admin.layouts.master')

<style>
/* Remove Bootstrap's default arrow */
.accordion-button::after {
    display: none !important;
}
</style>

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">PROFILE UPDATE</h3>
                    <div class="card-actions">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-default">
                            <i class="fa-solid fa-arrow-left me-2"></i>
                            Back
                        </a>
                    </div>
                </div>
                <div class="card-body">

                    <div class="add_course_basic_info">

                        <form action="{{ route('admin.profile.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="general_form_input">
                                        <label for="#">Image</label><br>
                                        <img src="{{ asset($user->image) }}"
                                            style="width: 150px !important; height: 150px !important; object-fit: contain !important; display: inline-block !important;">
                                        <input type="file" name="image" class="form-control mt-2">
                                        <x-input-error :messages="$errors->get('image')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                               
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Name</label>
                                        <input type="text" name="name" value="{{ $user->name }}" class="form-control">
                                        <x-input-error :messages="$errors->get('name')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Gender</label>
                                        <select class="form-control form-select" name="gender">
                                            <option @selected($user->gender == 'male') value="male">Male</option>
                                            <option @selected($user->gender == 'female') value="female">Female</option>
                                            <option @selected($user->gender == 'other') value="other">Other</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('gender')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                 <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Phone</label>
                                        <input type="text" name="phone" value="{{ $user->phone }}" class="form-control">
                                        <x-input-error :messages="$errors->get('phone')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                 <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Email</label>
                                        <input type="text" name="email" value="{{ $user->email }}" class="form-control">
                                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Date of Birth</label>
                                        <input type="date" name="dob" value="{{ $user->dob }}" class="form-control">
                                        <x-input-error :messages="$errors->get('dob')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#">Country</label>
                                        <select name="country_id" class="form-control form-select">
                                            <option value="">Select a country</option>
                                            @foreach($countries as $country)
                                                <option value="{{ $country->id }}" {{ old('country_id', $user->country_id) == $country->id ? 'selected' : '' }}>
                                                    {{ $country->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('country_id')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#">City</label>
                                        <input type="text" name="city" value="{{ $user->city }}" class="form-control">
                                        <x-input-error :messages="$errors->get('city')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#">Post Code</label>
                                        <input type="text" name="post_code" value="{{ $user->post_code }}" class="form-control">
                                        <x-input-error :messages="$errors->get('post_code')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#">Address</label>
                                        <input type="text" name="address" value="{{ $user->address }}" class="form-control">
                                        <x-input-error :messages="$errors->get('address')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                 
                            </div>

                            <div class="row">

                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label>Enter password</label>
                                        <input type="password" name="password" class="form-control" placeholder="Enter your password">
                                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label>Confirm password</label>
                                        <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm your password">
                                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-danger small" />
                                    </div>
                                </div>

                            </div>

                            @can('edit_admin_profile')
                                <div class="row">
                                
                                    <div class="col-xl-12 mt-3">
                                        <div class="add_course_basic_info_input">
                                            <div class="add_course_basic_info_input d-flex gap-2">
                                                <button type="submit" name="action" value="save_exit" class="btn btn-default mt_20">Update</button>
                                                <button type="submit" name="action" value="save_stay" class="btn btn-secondary mt_20">Update & Stay</button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            @endcan

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
