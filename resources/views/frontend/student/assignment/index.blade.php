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
                                <h5>Assignments</h5>
                                <p></p>
                                {{-- <a class="common_btn" href="{{ route('instructor.groups.create') }}">+ add course</a> --}}
                            </div>
                        </div>


                        <div class="wsus__dash_course_table">
                            <div class="row">
                                <div class="col-12 p-4">
                                    
                                    @foreach($modulesGroupedByCourse as $group)
                                        @php
                                            $course = $group['course'];
                                            $modules = $group['modules'];
                                        @endphp

                                        <h4 class="mb-3 text-muted">{{ $course->title }} ({{ $course->level->name }})</h4>

                                        <div class="table-responsive p-1 mb-5 border rounded">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th style="width:10px">#</th>
                                                        <th>Assignment</th>
                                                        <th>Submissions</th>
                                                        <th>Upload</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($modules as $index => $module)
                                                        @php $submission = $module->submissions->first(); @endphp

                                                        <tr>
                                                            <td><p>{{ $loop->iteration }}</p></td>

                                                            <td class="text-nowrap p-2">
                                                                <p class="mb-1">{{ $module->title }}</p>
                                                                <p class="mb-2"><small class="text-muted">{{ $course->title }} ({{ $course->level->name }})</small></p>
                                                                <p class="mb-1">
                                                                    @if($module->assignment_file)
                                                                        <small><a href="{{ asset($module->assignment_file) }}" target="_blank"><i class="fa-solid fa-file-pdf"></i></i> Assignment</a></small><br>
                                                                    @endif
                                                                </p>
                                                                <p>
                                                                    @if($module->sample_assignment_file)
                                                                        <small><a href="{{ asset($module->sample_assignment_file) }}" target="_blank"><i class="fa-solid fa-file-pdf"></i></i> Sample Assignment</a></small>
                                                                    @endif
                                                                </p>
                                                            </td>

                                                            
                                                            <td class="pb-0 pt-3">

                                                                @forelse($module->submissions as $submission)

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
                                                                                    <li>
                                                                                        <a href="{{ asset($file->file) }}" target="_blank">
                                                                                            <i class="fa-solid {{ getFileIcon($file->extension) }}"></i>
                                                                                            {{ basename($file->file) }}
                                                                                        </a>
                                                                                    </li>
                                                                                @endforeach
                                                                            </ul>
                                                                        </div>

                                                                        @if($submission && $submission->evaluated_at)
                                                                            <p class="fw-medium">Instructor's Evaluation ({{ $submission->evaluated_at ? \Carbon\Carbon::parse($submission->evaluated_at)->format('d-m-Y') : '' }})</p>
                                                                        @endif
                                                                            
                                                                        <div class="mt-1 mb-3">
                                                                            <ul class="ms-4">
                                                                                @if ($submission && $submission->feedback_file)
                                                                                    <li>
                                                                                        <a href="{{ asset($submission->feedback_file) }}" target="_blank">
                                                                                            <i class="fa-solid {{ getFileIcon($file->extension) }}"></i>
                                                                                            {{ basename($submission->feedback_file) }}
                                                                                        </a>
                                                                                    </li>
                                                                                @endif
                                                                                @if ($submission && $submission->plagiarism_report)
                                                                                    <li>
                                                                                        <a href="{{ asset($submission->plagiarism_report) }}" target="_blank">
                                                                                            <i class="fa-solid {{ getFileIcon($file->extension) }}"></i>
                                                                                            {{ basename($submission->plagiarism_report) }}
                                                                                        </a>
                                                                                    </li>
                                                                                @endif
                                                                            </ul>
                                                                        </div>

                                                                    </div>
                                                                @empty
                                                                    <span class="text-muted">No submissions</span>
                                                                @endforelse
                                                            </td>

                                                            <td class="text-nowrap px-3">
                                                                <p>
                                                                    @php
                                                                        $latest = $module->submissions->sortByDesc('created_at')->first();
                                                                        $canUpload = !$latest || $latest->extra_attempt == 1;
                                                                    @endphp

                                                                    @if($canUpload)
                                                                        @can('edit_student_assignments')
                                                                            <form action="{{ route('student.assignment.store', $module->id) }}" method="POST" enctype="multipart/form-data" id="upload-form-{{ $module->id }}">
                                                                                @csrf
                                                                                <label class="btn btn-primary">
                                                                                    Choose Files
                                                                                    <input type="file" name="files[]" class="form-control d-none" multiple onchange="document.getElementById('upload-form-{{ $module->id }}').submit();" />
                                                                                </label>
                                                                            </form>
                                                                        @endcan
                                                                    @endif
                                                                </p>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endforeach

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
