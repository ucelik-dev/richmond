
@extends('frontend.layouts.master')

@section('content')

    <section class="wsus__sign_in">
        <div class="row align-items-center">
            <div class="col-xxl-5 col-xl-6 col-lg-6 wow fadeInLeft">
                <div class="wsus__sign_img">
                    <img src="{{ asset('frontend/assets/images/login_img_1.jpg') }}" alt="login" class="img-fluid">
                    <a href="{{ url('/') }}">
                        <img src="{{ asset('frontend/assets/images/preloader2.png') }}" alt="EduCore" class="img-fluid">
                    </a>
                </div>
            </div>
            <div class="col-xxl-4 col-xl-5 col-lg-6 col-md-9 m-auto wow fadeInRight">
                <div class="wsus__sign_form_area">

                    <x-auth-session-status class="mb-4" :status="session('status')" />
                    
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-home" role="tabpanel"
                            aria-labelledby="pills-home-tab" tabindex="0">
                            <form action="{{ route('login') }}" method="POST">
                                @csrf

                                <h2 class="mb-4">Log in<span>!</span></h2>
                                
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="wsus__login_form_input">
                                          
                                            <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" required>
                                            <x-input-error :messages="$errors->get('email')" class="mt-2 text-danger" />
                                        </div>
                                    </div>
                                    <div class="col-xl-12">
                                        <div class="wsus__login_form_input">
                                            <input type="password" name="password" placeholder="Password" required>
                                            <x-input-error :messages="$errors->get('password')" class="mt-2 text-danger" />
                                        </div>
                                    </div>
                                    <div class="col-xl-12">
                                        <div class="wsus__login_form_input">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="" name="remember" id="flexCheckDefault">
                                                    <label class="form-check-label" for="flexCheckDefault">Remember Me</label>
                                                </div>

                                                <label><a href="{{ route('password.request') }}">Forgot Password?</a></label>
                                            </div>
                                            
                                            <button type="submit" class="common_btn">Sign In</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            {{-- <p class="or">or</p>
                            <ul class="social_login d-flex flex-wrap">
                                <li>
                                    <a href="#">
                                        <span><img src="images/google_icon.png" alt="Google" class="img-fluid"></span>
                                        Google
                                    </a>
                                </li>
                            </ul> --}}
                            <p class="create_account">Don't have an account? <a href="{{ route('register') }}">Create an account</a></p>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
        <a class="back_btn" href="index.html">Back to Home</a>
    </section>
    
@endsection

