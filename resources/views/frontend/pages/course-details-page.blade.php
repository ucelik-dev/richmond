@extends('frontend.layouts.master')

@section('content')

    <!--===========================
        BREADCRUMB START
    ============================-->
    <section class="wsus__breadcrumb course_details_breadcrumb" style="background: url(images/breadcrumb_bg.jpg);">
        <div class="wsus__breadcrumb_overlay">
            <div class="container">
                <div class="row">
                    <div class="col-12 wow fadeInUp">
                        <div class="wsus__breadcrumb_text">
                            
                            <h1>{{ $course->title }}</h1>
                            <ul class="list">
                                <li>Undergraduate</li>
                                <li>{{ $course->level->name }}</li>
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
        COURSES DETAILS START
    ============================-->
    <section class="wsus__courses_details pb_120 xs_pb_100">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 wow fadeInLeft">
                    <div class="wsus__courses_details_area mt_40">

                        <ul class="nav nav-pills mb_40" id="pills-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="pills-overview-tab" data-bs-toggle="pill"
                                    data-bs-target="#pills-overview" type="button" role="tab" aria-controls="pills-overview"
                                    aria-selected="true">Overview</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pills-curriculum-tab" data-bs-toggle="pill"
                                    data-bs-target="#pills-curriculum" type="button" role="tab"
                                    aria-controls="pills-curriculum" aria-selected="false">Curriculum</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pills-description-tab" data-bs-toggle="pill"
                                    data-bs-target="#pills-description" type="button" role="tab"
                                    aria-controls="pills-description" aria-selected="false">Description</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pills-learning-outcomes-tab" data-bs-toggle="pill"
                                    data-bs-target="#pills-learning-outcomes" type="button" role="tab"
                                    aria-controls="pills-learning-outcomes" aria-selected="false">Learning Outcomes</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pills-disabled-tab" data-bs-toggle="pill"
                                    data-bs-target="#pills-disabled" type="button" role="tab"
                                    aria-controls="pills-disabled" aria-selected="false">FAQs</button>
                            </li>
                        </ul>

                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-overview" role="tabpanel"
                                aria-labelledby="pills-overview-tab" tabindex="0">
                                <div class="wsus__courses_overview box_area">
                                    <h3>Course Overview</h3>
                                    <p>{!! $course->overview !!}</p>
                                    <p>{!! $course->overview_details !!}</p>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="pills-curriculum" role="tabpanel"
                                aria-labelledby="pills-curriculum-tab" tabindex="0">
                                <div class="wsus__courses_curriculum box_area">
                                    <h3>Course Curriculum</h3>
                                    <div class="accordion" id="accordionExample">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapseOne" aria-expanded="true"
                                                    aria-controls="collapseOne">
                                                    Course Prelude & EduCore Learning Presentation
                                                </button>
                                            </h2>
                                            <div id="collapseOne" class="accordion-collapse collapse show"
                                                data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <ul>
                                                        <li class="active">
                                                            <p>Brush up on Java concepts</p>
                                                            <span class="right_text">Preview</span>
                                                        </li>
                                                        <li>
                                                            <a href="">User Experience Fundamentals Course</a>
                                                            <span class="right_text">24 minutes</span>
                                                        </li>
                                                        <li>
                                                            <p>Brisk Guide to Using Pivot Tables in Excel</p>
                                                            <span class="right_text">7 minutes</span>
                                                        </li>
                                                        <li>
                                                            <p>User-Centric Design Fundamentals</p>
                                                            <span class="right_text">21 minutes</span>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapseTwo"
                                                    aria-expanded="false" aria-controls="collapseTwo">
                                                    Essential HTML Building Elements
                                                </button>
                                            </h2>
                                            <div id="collapseTwo" class="accordion-collapse collapse"
                                                data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <ul>
                                                        <li class="active">
                                                            <p>Brush up on Java concepts</p>
                                                            <span class="right_text">Preview</span>
                                                        </li>
                                                        <li>
                                                            <a href="">User Experience Fundamentals Course</a>
                                                            <span class="right_text">24 minutes</span>
                                                        </li>
                                                        <li>
                                                            <p>Brisk Guide to Using Pivot Tables in Excel</p>
                                                            <span class="right_text">7 minutes</span>
                                                        </li>
                                                        <li>
                                                            <p>User-Centric Design Fundamentals</p>
                                                            <span class="right_text">21 minutes</span>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapseThree"
                                                    aria-expanded="false" aria-controls="collapseThree">
                                                    Fundamental Programming Idea
                                                </button>
                                            </h2>
                                            <div id="collapseThree" class="accordion-collapse collapse"
                                                data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <ul>
                                                        <li class="active">
                                                            <p>Brush up on Java concepts</p>
                                                            <span class="right_text">Preview</span>
                                                        </li>
                                                        <li>
                                                            <a href="">User Experience Fundamentals Course</a>
                                                            <span class="right_text">24 minutes</span>
                                                        </li>
                                                        <li>
                                                            <p>Brisk Guide to Using Pivot Tables in Excel</p>
                                                            <span class="right_text">7 minutes</span>
                                                        </li>
                                                        <li>
                                                            <p>User-Centric Design Fundamentals</p>
                                                            <span class="right_text">21 minutes</span>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="pills-description" role="tabpanel"
                                aria-labelledby="pills-description-tab" tabindex="0">
                                <div class="wsus__courses_curriculum box_area">
                                    <h3>Course Description</h3>
                                    {!! $course->description !!}
                                </div>
                            </div>
                            <div class="tab-pane fade" id="pills-learning-outcomes" role="tabpanel"
                                aria-labelledby="pills-learning-outcomes-tab" tabindex="0">
                                <div class="wsus__courses_curriculum box_area">
                                    <h3>Learning Outcomes</h3>
                                    {!! $course->learning_outcomes !!}
                                </div>
                            </div>
                            <div class="tab-pane fade" id="pills-disabled" role="tabpanel"
                                aria-labelledby="pills-disabled-tab" tabindex="0">
                                <div class="wsus__course_faq box_area">
                                    <div class="accordion accordion-flush" id="accordionFlushExample">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#flush-collapseOne" aria-expanded="false"
                                                    aria-controls="flush-collapseOne">
                                                    How long it take to create a video course?
                                                </button>
                                            </h2>
                                            <div id="flush-collapseOne" class="accordion-collapse collapse show"
                                                data-bs-parent="#accordionFlushExample">
                                                <div class="accordion-body">
                                                    Sed mi leo, accumsan vel ante at, viverra placerat nulla. Donec
                                                    pharetra rutrum
                                                    ullamcorpe Ut eget convallis mi. Sed cursus aliquam eitu Nula sed
                                                    allium lectus
                                                    fermentum enim Nam maximus pretium consectetu lacinia finibus ipsum,
                                                    eget
                                                    fermentum nulla Pellentesque id facilisis magna dictum.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo"
                                                    aria-expanded="false" aria-controls="flush-collapseTwo">
                                                    What kind of support does EduCore provide?
                                                </button>
                                            </h2>
                                            <div id="flush-collapseTwo" class="accordion-collapse collapse"
                                                data-bs-parent="#accordionFlushExample">
                                                <div class="accordion-body">
                                                    Sed mi leo, accumsan vel ante at, viverra placerat nulla. Donec
                                                    pharetra rutrum
                                                    ullamcorpe Ut eget convallis mi. Sed cursus aliquam eitu Nula sed
                                                    allium lectus
                                                    fermentum enim Nam maximus pretium consectetu lacinia finibus ipsum,
                                                    eget
                                                    fermentum nulla Pellentesque id facilisis magna dictum.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#flush-collapseThree"
                                                    aria-expanded="false" aria-controls="flush-collapseThree">
                                                    How long do I get support & updates?
                                                </button>
                                            </h2>
                                            <div id="flush-collapseThree" class="accordion-collapse collapse"
                                                data-bs-parent="#accordionFlushExample">
                                                <div class="accordion-body">Placeholder content for this accordion,
                                                    Sed mi leo, accumsan vel ante at, viverra placerat nulla. Donec
                                                    pharetra rutrum
                                                    ullamcorpe Ut eget convallis mi. Sed cursus aliquam eitu Nula sed
                                                    allium lectus
                                                    fermentum enim Nam maximus pretium consectetu lacinia finibus ipsum,
                                                    eget
                                                    fermentum nulla Pellentesque id facilisis magna dictum.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#flush-collapseThree3"
                                                    aria-expanded="false" aria-controls="flush-collapseThree">
                                                    How can I contact a school directly?
                                                </button>
                                            </h2>
                                            <div id="flush-collapseThree3" class="accordion-collapse collapse"
                                                data-bs-parent="#accordionFlushExample">
                                                <div class="accordion-body">
                                                    Sed mi leo, accumsan vel ante at, viverra placerat nulla. Donec
                                                    pharetra rutrum
                                                    ullamcorpe Ut eget convallis mi. Sed cursus aliquam eitu Nula sed
                                                    allium lectus
                                                    fermentum enim Nam maximus pretium consectetu lacinia finibus ipsum,
                                                    eget
                                                    fermentum nulla Pellentesque id facilisis magna dictum.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-8 wow fadeInRight">
                    <div class="wsus__courses_sidebar">
                        <div class="wsus__courses_sidebar_video">
                            <img src="{{ asset($course->thumbnail) }}" alt="Video" class="img-fluid">
                            @if($course->demo_video_source != NULL)
                                <a class="play_btn venobox vbox-item" data-autoplay="true" data-vbtype="video"
                                    href="{{ $course->demo_video_source }}">
                                    <img src="{{ asset('frontend/assets/images/play_icon_white.png') }}" alt="Play" class="img-fluid">
                                </a>
                            @endif
                        </div>
                        <h3 class="wsus__courses_sidebar_price">
                            @if($course->discount > 0)
                                <del>£{{ $course->price }}</del> £{{ $course->price - $course->discount }}
                            @else
                                £{{ $course->price }}
                            @endif

                         </h3>
                        <div class="wsus__courses_sidebar_list_info">
                            <ul>
                                <li>
                                    <p>
                                        <span><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-category"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4h6v6h-6z" /><path d="M14 4h6v6h-6z" /><path d="M4 14h6v6h-6z" /><path d="M17 17m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /></svg></span>
                                        Category
                                    </p>
                                    {{ $course->category->name }}
                                </li>
                                <li>
                                    <p>
                                        <span><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-menu-deep"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 6h16" /><path d="M7 12h13" /><path d="M10 18h10" /></svg></span>
                                        Level
                                    </p>
                                    {{ $course->level->name }}
                                </li>
                                <li>
                                    <p>
                                        <span><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-layers-difference"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M16 16v2a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2v-8a2 2 0 0 1 2 -2h2v-2a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-2" /><path d="M10 8l-2 0l0 2" /><path d="M8 14l0 2l2 0" /><path d="M14 8l2 0l0 2" /><path d="M16 14l0 2l-2 0" /></svg></span>
                                        Code
                                    </p>
                                    {{ $course->code }}
                                </li>
                                <li>
                                    <p>
                                        <span><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-certificate"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 15m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /><path d="M13 17.5v4.5l2 -1.5l2 1.5v-4.5" /><path d="M10 19h-5a2 2 0 0 1 -2 -2v-10c0 -1.1 .9 -2 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -1 1.73" /><path d="M6 9l12 0" /><path d="M6 12l3 0" /><path d="M6 15l2 0" /></svg></span>
                                        Credits
                                    </p>
                                    {{ $course->credits }}
                                </li>
                            </ul>
                            <a class="common_btn" href="#">Enroll The Course <i class="far fa-arrow-right"></i></a>
                        </div>

                        <div class="wsus__courses_sidebar_share_btn d-flex flex-wrap justify-content-between">
                            <a href="#" class="common_btn"><i class="far fa-heart"></i> Add to Wishlist</a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--===========================
        COURSES DETAILS END
    ============================-->

@endsection