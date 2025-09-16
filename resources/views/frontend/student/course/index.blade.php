@extends('frontend.layouts.master')

@section('content')

    <!--===========================
        DASHBOARD OVERVIEW START
    ============================-->
    <section class="wsus__dashboard mt_150 pb_50">
        <div class="container-fluid px-4">
            <div class="row">
                
                @include('frontend.student.sidebar')

                <div class="col-xl-10 col-md-8">
                    
                    <div class="wsus__dashboard_content">
                        <div class="wsus__dashboard_content_top">
                            <div class="wsus__dashboard_heading relative">
                                <h5>Enrolled Courses</h5>
                            </div>
                        </div>

                        <div class="wsus__dash_course_table">
                            <div class="row">
                                <div class="col-12 p-4">
                                    <div class="table-responsive p-1 mb-5 border rounded">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th>MODULES</th>
                                                    <th>HANDBOOK</th>
                                                    <th>MAPPING DOCUMENT</th>
                                                    <th>ASSIGNMENT<br>SPECIFICATION</th>
                                                    <th>CURRICULUM</th>
                                                    <th>WEBINAR</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                           
                                                @foreach($courses as $course)
                                                    <tr>
                                                        
                                                        <td class="image">
                                                            <div class="image_category">
                                                                <img src="{{ asset($course->logo) }}" alt="img" class="img-fluid w-100">
                                                            </div>
                                                            <p>{{ $course->title }}</p>
                                                            <p>({{ $course->level->name }})</p>
                                                        </td>
                                                        <td>
                                                            @foreach ($course->modules as $module)
                                                                <p class="m-0">
                                                                    - {{ $module->title }}
                                                                </p>
                                                            @endforeach
                                                        </td>
                                                        <td>
                                                            <a href="{{ asset($course->handbook_file) }}" target="_blank">
                                                                <i class="fa-solid fa-book fa-5x"></i>
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <a href="{{ asset($course->mapping_document) }}" target="_blank">
                                                                <i class="fa-solid fa-map fa-5x"></i>
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <a href="{{ asset($course->assignment_specification) }}" target="_blank">
                                                                <i class="fa-solid fa-file-lines fa-5x"></i>    
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <a href="{{ asset($course->curriculum) }}" target="_blank">
                                                                <i class="fa-solid fa-folder-open fa-5x"></i> 
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <a href="#" >
                                                                <i class="fa-solid fa-laptop fa-5x"></i> 
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
               
                    
                </div>
            </div>
        </div>
    </section>
    <!--===========================
        DASHBOARD OVERVIEW END
    ============================-->
    
@endsection