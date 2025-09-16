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
                                <h5>Students</h5>
                                <p>{{ $students->count() }}</p>
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
                                                    <th>COURSE</th>
                                                    <th>GROUP</th>
                                                    <th>BATCH</th>
                                                    <th>STATUS</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                               
                                                @foreach($students as $student)
                                                    <tr>
                                                        <td><p>{{ $loop->iteration }}</p></td>
                                                        <td class="text-nowrap">
                                                            <p class="fw-medium mb-2">{{ $student->name }}</p>
                                                            <p>{{ $student->email }}</p>
                                                            <p>{{ $student->phone }}</p>
                                                            <p>{{ $student->country?->name }}</p>
                                                        </td>

                                                        <td class="text-nowrap">
                                                            @foreach($student->enrollments as $enrollment)
                                                                @if($enrollment->course)
                                                                    <span class="badge bg-secondary text-secondary-fg">
                                                                        {{ $enrollment->course->title }} ({{ $enrollment->course->level->name }})
                                                                    </span><br>
                                                                @endif
                                                            @endforeach
                                                        </td>
                                                        
                                                        <td class="text-nowrap">
                                                            @foreach($student->enrollments as $enrollment)
                                                                @if($enrollment->group)
                                                                    <span class="badge bg-{{ $enrollment->group->color }} text-{{ $enrollment->group->color }}-fg">
                                                                        {{ str_replace('_', ' ', $enrollment->group->name) }}
                                                                    </span><br>
                                                                @endif
                                                            @endforeach
                                                        </td>

                                                        <td class="text-nowrap">
                                                            @foreach($student->enrollments as $enrollment)
                                                                @if($enrollment->batch)
                                                                    <span class="badge bg-{{ $enrollment->batch->color }} text-{{ $enrollment->batch->color }}-fg">
                                                                        {{ str_replace('_', ' ', $enrollment->batch->name) }}
                                                                    </span><br>
                                                                @endif
                                                            @endforeach
                                                        </td>
                                                       
                                                        <td class="text-nowrap">
                                                            <p>
                                                                <div>
                                                                    <div class="mb-2 text-nowrap">
                                                                        <span class="d-inline-block text-nowrap" style="width: 100px;">Account Status </span>
                                                                        <span>: 
                                                                            @if($student->account_status === 1)
                                                                                <span class="badge bg-success text-success-fg">Active</span>
                                                                            @elseif($student->account_status === 0)
                                                                                <span class="badge bg-danger text-danger-fg">Inactive</span>
                                                                            @endif
                                                                        </span>
                                                                    </div>
                                                                    <div class="text-nowrap">
                                                                        <span class="d-inline-block text-nowrap" style="width: 100px;">User Status </span>
                                                                        <span>: 
                                                                            <span class="badge bg-{{ $student->userStatus->color }} text-{{ $student->userStatus->color }}-fg">{{ ucwords($student->userStatus->name) }}</span>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </p>
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