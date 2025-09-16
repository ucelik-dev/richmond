
@extends('frontend.layouts.master')

@section('content')

    <section class="wsus__sign_in sign_up">
        <div class="row align-items-center">
            
            <div class="col-xxl-8 col-xl-8 col-lg-10 col-md-10  wow fadeInRight mx-auto mt-5">
                <div class="wsus__sign_form_area">
                 
                    <div class="tab-content" id="pills-tabContent">


                        <div class="tab-pane fade show active" id="pills-student" role="tabpanel"
                            aria-labelledby="pills-student-tab" tabindex="0">
                            <form action="{{ route('register') }}" method="POST">
                                @csrf

                                <h2 class="mb-4">Sign Up<span>!</span></h2>
                                
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="wsus__login_form_input">
                                            <label>Name</label>
                                            <input type="text" placeholder="Enter your name" name="name" value="{{ old('name') }}">
                                            <x-input-error :messages="$errors->get('name')" class="mt-2 text-danger small" />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="wsus__login_form_input">
                                            <label>Email</label>
                                            <input type="email" placeholder="Enter your email" name="email" value="{{ old('email') }}">
                                            <x-input-error :messages="$errors->get('email')" class="mt-2 text-danger small" />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="wsus__login_form_input">
                                            <label>Gender</label>
                                            <select name="gender" class="form-control form-select p-2">
                                                <option value="" disabled selected hidden>Select your gender</option>
                                                <option value="male">Male</option>
                                                <option value="female">Female</option>
                                                <option value="other">Other</option>
                                            </select>
                                            <x-input-error :messages="$errors->get('gender')" class="mt-2 text-danger small" />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="wsus__login_form_input">
                                            <label>Role</label>
                                            <select name="role" class="form-control form-select p-2">
                                                <option value="" disabled selected hidden>Select your role</option>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->id }}"> {{ ucwords($role->name) }} </option>
                                                @endforeach
                                            </select>
                                            <x-input-error :messages="$errors->get('role')" class="mt-2 text-danger small" />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="wsus__login_form_input">
                                            <label>Phone</label>
                                            <input type="text" placeholder="Enter your phone" name="phone" value="{{ old('phone') }}">
                                            <x-input-error :messages="$errors->get('phone')" class="mt-2 text-danger small" />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="wsus__login_form_input">
                                            <label>Date of Birth</label>
                                            <input type="date" placeholder="Enter your birthdate" name="dob" value="{{ old('dob') }}">
                                            <x-input-error :messages="$errors->get('dob')" class="mt-2 text-danger small" />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="wsus__login_form_input">
                                            <label>Postal Code</label>
                                            <input type="text" placeholder="Enter your Postal Code" name="post_code" value="{{ old('post_code') }}">
                                            <x-input-error :messages="$errors->get('post_code')" class="mt-2 text-danger small" />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="wsus__login_form_input">
                                            <label>Country</label>
                                            <input type="text" placeholder="Enter your country" name="country" value="{{ old('country') }}">
                                            <x-input-error :messages="$errors->get('country')" class="mt-2 text-danger small" />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="wsus__login_form_input">
                                            <label>City</label>
                                            <input type="text" placeholder="Enter your city" name="city" value="{{ old('city') }}">
                                            <x-input-error :messages="$errors->get('city')" class="mt-2 text-danger small" />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="wsus__login_form_input">
                                            <label>Address</label>
                                            <input type="text" placeholder="Enter your address" name="address" value="{{ old('address') }}">
                                            <x-input-error :messages="$errors->get('address')" class="mt-2 text-danger small" />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="wsus__login_form_input">
                                            <label>Enter password</label>
                                            <input type="password" name="password" placeholder="Enter your password">
                                            <x-input-error :messages="$errors->get('password')" class="mt-2 text-danger small" />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="wsus__login_form_input">
                                            <label>Confirm password</label>
                                            <input type="password" name="password_confirmation" placeholder="Confirm your password">
                                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-danger small" />
                                        </div>
                                    </div>
                                    <div class="col-xl-12">
                                        <div class="wsus__login_form_input">
                                            {{-- <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="flexCheckDefault">
                                                <label class="form-check-label" for="flexCheckDefault"> By clicking
                                                    Create
                                                    account, I agree that I have read and accepted the <a href="#">Terms
                                                        of
                                                        Use</a> and <a href="#">Privacy Policy.</a>
                                                </label>
                                            </div> --}}
                                            <button type="submit" class="common_btn">Sign Up</button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <p class="create_account">Already have an account? <a href="{{ route('login') }}">Sign In</a></p>
                            
                        </div>

                      

                    </div>
                </div>
            </div>
        </div>
        <a class="back_btn" href="index.html">Back to Home</a>
    </section>
    
@endsection