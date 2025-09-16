@extends('frontend.layouts.master')

@section('content')

    <!--===========================
        DASHBOARD OVERVIEW START
    ============================-->
    <section class="wsus__dashboard mt_150 pb_50">
        <div class="container-fluid px-4">
            <div class="row">
                
                @include('frontend.instructor.sidebar')

                <div class="col-xl-10 col-md-8">
                    
                    <div class="wsus__dashboard_content">
                        <div class="wsus__dashboard_content_top">
                            <div class="wsus__dashboard_heading relative">
                                <h5>Student Groups</h5>
                                <p>Manage your groups.</p>
                                {{-- <a class="common_btn" href="{{ route('instructor.groups.create') }}">+ add course</a> --}}
                            </div>
                        </div>


                        <div class="wsus__dash_course_table">
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-responsive p-4">
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th style="width:10px">#</th>
                                                    <th>NAME</th>
                                                    <th>COUNT</th>
                                                    <th>STATUS</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                               
                                                @foreach($groups as $group)
                                                    <tr>
                                                        <td><p>{{ $loop->iteration }}</p></td>
                                                        
                                                        <td>
                                                            <p>
                                                                @can('view_instructor_group_shares')
                                                                    <a href="{{ route('instructor.groups.group-shares.index', $group->id) }}">
                                                                        {{ str_replace('_', ' ', $group->name) }}
                                                                    </a>
                                                                @else
                                                                    <p>
                                                                        {{ str_replace('_', ' ', $group->name) }}
                                                                    </p>
                                                                @endcan
                                                        </p>
                                                        </td>
                                                       
                                                        <td><p>{{ $group->students->count() }}</p></td>

                                                        <td>
                                                            <span class="badge bg-success text-success-fg">Active: {{ $group->active_student_count }}</span><br>
                                                            <span class="badge bg-danger text-danger-fg">Inactive: {{ $group->inactive_student_count }}</span>
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