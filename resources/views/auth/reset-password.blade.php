
@extends('frontend.layouts.master')

@section('content')

    <section class="wsus__sign_in">
        <div class="row align-items-center">
            <div class="col-xxl-5 col-xl-6 col-lg-6 wow fadeInLeft">
                <div class="wsus__sign_img">
                    <img src="{{ asset('frontend/assets/images/login_img_1.jpg') }}" alt="login" class="img-fluid">
                    <a href="{{ url('/') }}">
                        <img src="{{ asset('frontend/assets/images/logo.png') }}" alt="EduCore" class="img-fluid">
                    </a>
                </div>
            </div>
            <div class="col-xxl-4 col-xl-5 col-lg-6 col-md-9 m-auto wow fadeInRight">
                <div class="wsus__sign_form_area">
                    
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-home" role="tabpanel"
                            aria-labelledby="pills-home-tab" tabindex="0">

                            <x-auth-session-status class="mb-4" :status="session('status')" />
                            
                            <form action="{{ route('password.store') }}" method="POST">
                                @csrf

                                <h2 class="mb-4">Reset Password<span>!</span></h2>
                                
                                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="wsus__login_form_input">
                                            <input type="email" name="email" value="{{ old('email', $request->email) }}" placeholder="Email" required>
                                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                        </div>
                                    </div>
                                    <div class="col-xl-12">
                                        <div class="wsus__login_form_input">
                                            <input type="password" name="password" placeholder="Password" required>
                                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                        </div>
                                    </div>
                                    <div class="col-xl-12">
                                        <div class="wsus__login_form_input">
                                            <input type="password" name="password_confirmation" placeholder="Confirm Password" required>
                                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                        </div>
                                    </div>
                                    <div class="col-xl-12">
                                        <div class="wsus__login_form_input">
                                            <button type="submit" class="common_btn">Reset Password</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
        <a class="back_btn" href="index.html">Back to Home</a>
    </section>
    
@endsection

