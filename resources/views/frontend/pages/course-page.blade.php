@extends('frontend.layouts.master')

@section('content')

        <!--===========================
        BREADCRUMB START
    ============================-->
    <section class="wsus__breadcrumb" style="background: url(images/breadcrumb_bg.jpg);">
        <div class="wsus__breadcrumb_overlay">
            <div class="container">
                <div class="row">
                    <div class="col-12 wow fadeInUp">
                        <div class="wsus__breadcrumb_text">
                            <h1>Our Courses</h1>
                            <ul>
                                <li><a href="{{ url('/') }}">Home</a></li>
                                <li>Our Courses</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--===========================
        BREADCRUMB END
    ============================-->


    <!--===========================
        COURSES PAGE START
    ============================-->
    <section class="wsus__courses mt_120 xs_mt_100 pb_120 xs_pb_100">
        <div class="container">
            <div class="row">
                <div class="col-xl-3 col-lg-4 col-md-8 order-2 order-lg-1 wow fadeInLeft">
                    <div class="wsus__sidebar">
                        <form action="#">
                            <div class="wsus__sidebar_search">
                                <input type="text" placeholder="Search Course">
                                <button type="submit">
                                    <img src="images/search_icon.png" alt="Search" class="img-fluid">
                                </button>
                            </div>

                            <div class="wsus__sidebar_category">
                                <h3>Categories</h3>
                                <ul class="category_list">
                                    <li class="">Bundle Courses
                                        <div class="wsus__sidebar_sub_category">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="flexCheckDefaultc1">
                                                <label class="form-check-label" for="flexCheckDefaultc1">
                                                    Game developments
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="flexCheckDefaultc2">
                                                <label class="form-check-label" for="flexCheckDefaultc2">
                                                    Apple
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="flexCheckDefaultc3">
                                                <label class="form-check-label" for="flexCheckDefaultc3">
                                                    Career developments
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="flexCheckDefaultc44">
                                                <label class="form-check-label" for="flexCheckDefaultc44">
                                                    Communication
                                                </label>
                                            </div>
                                        </div>
                                    </li>
                                    <li>Development
                                        <div class="wsus__sidebar_sub_category">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="flexCheckDefaultc5">
                                                <label class="form-check-label" for="flexCheckDefaultc5">
                                                    Game developments
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="flexCheckDefaultc6">
                                                <label class="form-check-label" for="flexCheckDefaultc6">
                                                    Apple
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="flexCheckDefaultc7">
                                                <label class="form-check-label" for="flexCheckDefaultc7">
                                                    Career developments
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="flexCheckDefaultc4">
                                                <label class="form-check-label" for="flexCheckDefaultc4">
                                                    Communication
                                                </label>
                                            </div>
                                        </div>
                                    </li>
                                    <li>Live class
                                        <div class="wsus__sidebar_sub_category">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="flexCheckDefaultc9">
                                                <label class="form-check-label" for="flexCheckDefaultc9">
                                                    Game developments
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="flexCheckDefaultc10">
                                                <label class="form-check-label" for="flexCheckDefaultc10">
                                                    Apple
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="flexCheckDefaultc11">
                                                <label class="form-check-label" for="flexCheckDefaultc11">
                                                    Career developments
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="flexCheckDefaultc12">
                                                <label class="form-check-label" for="flexCheckDefaultc12">
                                                    Communication
                                                </label>
                                            </div>
                                        </div>
                                    </li>
                                    <li>IT & Software
                                        <div class="wsus__sidebar_sub_category">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="flexCheckDefaultc13">
                                                <label class="form-check-label" for="flexCheckDefaultc13">
                                                    Game developments
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="flexCheckDefaultc14">
                                                <label class="form-check-label" for="flexCheckDefaultc14">
                                                    Apple
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="flexCheckDefaultc15">
                                                <label class="form-check-label" for="flexCheckDefaultc15">
                                                    Career developments
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="flexCheckDefaultc66">
                                                <label class="form-check-label" for="flexCheckDefaultc66">
                                                    Communication
                                                </label>
                                            </div>
                                        </div>
                                    </li>
                                    <li>Health & Fitness
                                        <div class="wsus__sidebar_sub_category">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="flexCheckDefaultc17">
                                                <label class="form-check-label" for="flexCheckDefaultc17">
                                                    Game developments
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="flexCheckDefaultc18">
                                                <label class="form-check-label" for="flexCheckDefaultc18">
                                                    Apple
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="flexCheckDefaultc19">
                                                <label class="form-check-label" for="flexCheckDefaultc19">
                                                    Career developments
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="flexCheckDefaultc20">
                                                <label class="form-check-label" for="flexCheckDefaultc20">
                                                    Communication
                                                </label>
                                            </div>
                                        </div>
                                    </li>
                                    <li>Data Science
                                        <div class="wsus__sidebar_sub_category">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="flexCheckDefaultc21">
                                                <label class="form-check-label" for="flexCheckDefaultc21">
                                                    Game developments
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="flexCheckDefaultc22">
                                                <label class="form-check-label" for="flexCheckDefaultc22">
                                                    Apple
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="flexCheckDefaultc23">
                                                <label class="form-check-label" for="flexCheckDefaultc23">
                                                    Career developments
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                    id="flexCheckDefaultc24">
                                                <label class="form-check-label" for="flexCheckDefaultc24">
                                                    Communication
                                                </label>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>

                            <div class="wsus__sidebar_course_lavel">
                                <h3>Level</h3>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                                    <label class="form-check-label" for="flexCheckDefault">
                                        Higher
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault2">
                                    <label class="form-check-label" for="flexCheckDefault2">
                                        Medium
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault3">
                                    <label class="form-check-label" for="flexCheckDefault3">
                                        Lowest
                                    </label>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-xl-9 col-lg-8 order-lg-1">
                    <div class="wsus__page_courses_header wow fadeInUp">
                        <p>Showing <span>1-9</span> Of <span>62</span> Results</p>
                        <form action="#">
                            <p>Sort-by:</p>
                            <select class="select_js">
                                <option value="">Regular</option>
                                <option value="">Top Rated</option>
                                <option value="">Popular Courses</option>
                                <option value="">Recent Courses</option>
                            </select>
                        </form>
                    </div>
                    <div class="row">

                        @forelse($courses as $course)
                        <div class="col-xl-4 col-md-6 wow fadeInUp">
                            <div class="wsus__single_courses_3">
                                <div class="wsus__single_courses_3_img">
                                    <img src="{{ asset($course->thumbnail) }}" alt="Courses" class="img-fluid">
                                    <span class="time"><i class="far fa-clock"></i> 15 Hours</span>
                                </div>
                                <div class="wsus__single_courses_text_3">
                                    <a class="title" href="{{ route('courses.show', $course->id) }}">{{ $course->title }}</a>
                                    <ul>
                                        <li>Undergraduate</li>
                                        <li>{{ $course->level->name }}</li>
                                    </ul>
                                </div>
                                <div class="wsus__single_courses_3_footer">
                                    <a class="common_btn" href="#">Enroll <i class="far fa-arrow-right"></i></a>
                                    <p>
                                        @if($course->discount > 0)
                                            <del>£{{ $course->price }}</del> £{{ $course->price - $course->discount }}
                                        @else
                                            £{{ $course->price }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        @empty
                            <p>No course found.</p>
                        @endforelse

                    </div>
                    <div class="wsus__pagination mt_50 wow fadeInUp">
                        <nav aria-label="Page navigation example">
                            <ul class="pagination">
                                <li class="page-item">
                                    <a class="page-link" href="#" aria-label="Previous">
                                        <i class="far fa-arrow-left"></i>
                                    </a>
                                </li>
                                <li class="page-item"><a class="page-link active" href="#">01</a></li>
                                <li class="page-item"><a class="page-link" href="#">02</a></li>
                                <li class="page-item"><a class="page-link" href="#">03</a></li>
                                <li class="page-item">
                                    <a class="page-link" href="#" aria-label="Next">
                                        <i class="far fa-arrow-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--===========================
        COURSES PAGE END
    ============================-->

@endsection