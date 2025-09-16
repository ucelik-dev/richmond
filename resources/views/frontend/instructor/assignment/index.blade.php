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
                                <h5>Assignment Submissions</h5>
                                <p></p>
                                {{-- <a class="common_btn" href="{{ route('instructor.groups.create') }}">+ add course</a> --}}
                            </div>
                        </div>

                        <div class="wsus__dash_course_table">
                            <div class="row">
                                <div class="col-12 p-4">
                                        
                                        <div class="table-responsive p-1 mb-5 border rounded">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th style="width:10px">#</th>
                                                        <th>Student</th>
                                                        <th>Module / Course</th>
                                                        <th>Submissions</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($courses as $course)
                                                        @foreach($course->modules as $module)
                                                            @foreach($module->submissions as $submission)
                                                                <tr>
                                                                    <td><p>{{ $submission->id }}</p></td>

                                                                    {{-- Student --}}
                                                                    <td class="text-nowrap p-2">
                                                                        <p>
                                                                            <strong>{{ $submission->student->name }}</strong><br>
                                                                            <small>{{ $submission->student->email }}</small><br>
                                                                            @if($submission->student->phone) <small>{{ $submission->student->phone }}</small> @endif
                                                                        </p>
                                                                    </td>

                                                                    {{-- Module --}}
                                                                    <td class="text-nowrap p-2">
                                                                        <p class="mb-1">{{ $module->title }}</p>
                                                                        <p class="mb-2"><small class="text-muted">{{ $course->title }} ({{ $course->level->name }})</small></p>
                                                                        <p class="mb-1">
                                                                            @if($module->assignment_file)
                                                                                <small><a href="{{ asset($module->assignment_file) }}" target="_blank"><i class="fa-solid fa-file-pdf"></i> Assignment</a></small><br>
                                                                            @endif
                                                                        </p>
                                                                        <p>
                                                                            @if($module->sample_assignment_file)
                                                                                <small><a href="{{ asset($module->sample_assignment_file) }}" target="_blank"><i class="fa-solid fa-file-pdf"></i> Sample Assignment</a></small>
                                                                            @endif
                                                                        </p>
                                                                    </td>

                                                                    <td class="pb-0 pt-3">

                                                                            <div class="mb-2">
                                                                                @if($submission)
                                                                                    @if($submission->grade == 'pending')
                                                                                        <span class="badge bg-yellow">{{ ucfirst($submission->grade) }}</span>
                                                                                    @elseif($submission->grade == 'failed')
                                                                                        <span class="badge bg-danger">{{ ucfirst($submission->grade) }}</span>
                                                                                    @else
                                                                                        <span class="badge bg-success">{{ ucfirst($submission->grade) }}</span>
                                                                                    @endif
                                                                                @endif
                                                                            </div>

                                                                            <div class="mb-3 p-2 pb-0 border rounded">
                                                                                
                                                                                <p class="fw-medium">Student's Submission ({{ $submission->created_at ? \Carbon\Carbon::parse($submission->created_at)->format('d-m-Y') : '' }})</p>
                                                                                    
                                                                                <div class="mt-1 mb-3">
                                                                                    <ul class="ms-4">
                                                                                        @foreach ($submission->files as $file)
                                                                                            <li style="word-break: break-word;">
                                                                                                <span class="d-inline-flex align-items-start">
                                                                                                    <a href="{{ asset($file->file) }}" target="_blank">
                                                                                                        <i class="fa-solid {{ getFileIcon($file->extension) }} mt-1"></i>
                                                                                                        {{ basename($file->file) }}
                                                                                                    </a>
                                                                                                </span>
                                                                                            </li>
                                                                                        @endforeach
                                                                                    </ul>
                                                                                </div>

                                                                                @if($submission && $submission->evaluated_at)
                                                                                    <div class="d-flex align-items-center gap-1">
                                                                                        <p class="fw-medium">
                                                                                            Instructor's Evaluation ({{ $submission->evaluated_at ? \Carbon\Carbon::parse($submission->evaluated_at)->format('d-m-Y') : '' }})
                                                                                        </p>

                                                                                        @can('delete_instructor_assignments')
                                                                                            <form action="{{ route('instructor.assignment.evaluation.destroy', $submission->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete evaluation files?')" class="d-inline">
                                                                                                @csrf
                                                                                                @method('DELETE')
                                                                                                <button type="submit" class="btn btn-sm btn-link text-danger p-0">
                                                                                                    <i class="fa-solid fa-trash fa-sm"></i>
                                                                                                </button>
                                                                                            </form>
                                                                                        @endcan
                                                                                    </div>
                                                                                @endif
                                                                                    <div class="mt-1 mb-3">
                                                                                        <ul class="ms-4">
                                                                                            @if ($submission && $submission->feedback_file)
                                                                                                <li>
                                                                                                    <span class="d-inline-flex align-items-start">
                                                                                                        <a href="{{ asset($submission->feedback_file) }}" target="_blank">
                                                                                                            <i class="fa-solid {{ getFileIcon($file->extension) }}"></i>
                                                                                                            {{ basename($submission->feedback_file) }}
                                                                                                        </a>
                                                                                                    </span>
                                                                                                </li>
                                                                                            @endif
                                                                                            @if ($submission && $submission->verification_file)
                                                                                                <li>
                                                                                                    <span class="d-inline-flex align-items-start">
                                                                                                        
                                                                                                        <a href="{{ asset($submission->verification_file) }}" target="_blank">
                                                                                                            <i class="fa-solid {{ getFileIcon($file->extension) }}"></i>
                                                                                                            {{ basename($submission->verification_file) }}
                                                                                                        </a>
                                                                                                    </span>
                                                                                                </li>
                                                                                            @endif
                                                                                            @if ($submission && $submission->plagiarism_report)
                                                                                                <li>
                                                                                                    <span class="d-inline-flex align-items-start">
                                                                                                        <a href="{{ asset($submission->plagiarism_report) }}" target="_blank">
                                                                                                            <i class="fa-solid {{ getFileIcon($file->extension) }}"></i>
                                                                                                            {{ basename($submission->plagiarism_report) }}
                                                                                                        </a>
                                                                                                    </span>
                                                                                                </li>
                                                                                            @endif
                                                                                        </ul>
                                                                                    </div>
                                                                                

                                                                            </div>
                                                                        
                                                                    </td>

                                                                    {{-- Actions --}}
                                                                    <td class="text-nowrap px-3">
                                                                        @can('edit_instructor_assignments')
                                                                            <a href="{{ route('instructor.assignment.edit', $submission->id) }}" class="btn btn-sm btn-primary">Evaluate</a>
                                                                        @endcan
                                                                    </td>
                                                                </tr>
                                                         @endforeach
                                                    @endforeach
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
