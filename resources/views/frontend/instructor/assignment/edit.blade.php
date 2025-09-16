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
                            <div class="wsus__dashboard_heading d-flex justify-content-between align-items-center">
                                <h5>Evaluate Submission</h5>
                                <a href="{{ route('instructor.assignment.index') }}" class="btn btn-primary">
                                    <i class="fa-solid fa-arrow-left me-2"></i>
                                    Back
                                </a>
                            </div>
                        </div>

                        <div class="wsus__dash_course_table">
                            <div class="row">
                                <div class="col-12 p-4 pt-0">

                                    <p>
                                        <p class="mt-3 mb-0"><strong>Module:</strong> {{ $submission->module->title ?? '-' }}</p>
                                        <p class="mb-0"><strong>Student:</strong> {{ $submission->student->name ?? '-' }}</p>
                                        <p class="mb-0"><strong>Submitted:</strong> {{ $submission->created_at ?? '-' }}</p>
                                    </p>

                                    <hr class="mb-1">
                                    
                                    <form action="{{ route('instructor.assignment.update', $submission->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')

                                        <div class="row">
                                            <div class="col-xl-6">
                                                <div class="wsus__login_form_input">
                                                    <label for="evaluated_at" class="form-label label-required">Evaluation Date</label>
                                                    <input type="date" name="evaluated_at" class="form-control" value="{{ $submission->evaluated_at ? \Carbon\Carbon::parse($submission->evaluated_at)->format('Y-m-d') : '' }}">
                                                    <x-input-error :messages="$errors->get('evaluated_at')" class="mt-2 text-danger small" />
                                                </div>
                                            </div>

                                            <div class="col-xl-6">
                                                <div class="wsus__login_form_input">
                                                    <label for="grade" class="form-label label-required">Grade</label>
                                                    <select name="grade" class="form-select" required>
                                                        @foreach(['pending', 'passed', 'merit', 'distinction', 'failed'] as $grade)
                                                            <option value="{{ $grade }}" {{ $submission->grade == $grade ? 'selected' : '' }}>
                                                                {{ ucfirst($grade) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <x-input-error :messages="$errors->get('grade')" class="mt-2 text-danger small" />
                                                </div>
                                            </div>

                                            <div class="col-xl-4">
                                                <div class="wsus__login_form_input">
                                                    <label class="form-label">Feedback File
                                                        @if($submission->feedback_file)
                                                            ( <a href="{{ asset($submission->feedback_file) }}" target="_blank" class="text-decoration-none">View</a> )
                                                        @endif
                                                    </label>
                                                    <input type="file" name="feedback_file" class="form-control">
                                                    <x-input-error :messages="$errors->get('feedback_file')" class="mt-2 text-danger small" />
                                                </div>
                                            </div>

                                            <div class="col-xl-4">
                                                <div class="wsus__login_form_input">
                                                    <label class="form-label">Verification File
                                                        @if($submission->verification_file)
                                                            ( <a href="{{ asset($submission->verification_file) }}" target="_blank" class="text-decoration-none">View</a> )
                                                        @endif
                                                    </label>
                                                    <input type="file" name="verification_file" class="form-control">
                                                    <x-input-error :messages="$errors->get('verification_file')" class="mt-2 text-danger small" />
                                                </div>
                                            </div>

                                            <div class="col-xl-4">
                                                <div class="wsus__login_form_input">
                                                    <label class="form-label">Plagiarism Report
                                                        @if($submission->plagiarism_report)
                                                            ( <a href="{{ asset($submission->plagiarism_report) }}" target="_blank" class="text-decoration-none">View</a> )
                                                        @endif
                                                    </label>
                                                    <input type="file" name="plagiarism_report" class="form-control">
                                                    <x-input-error :messages="$errors->get('plagiarism_report')" class="mt-2 text-danger small" />
                                                </div>
                                            </div>

                                            <div class="col-xl-12">
                                                <div class="wsus__login_form_input">
                                                    <label class="form-label">Feedback</label>
                                                    <textarea name="feedback" rows="4" class="form-control">{{ $submission->feedback }}</textarea>
                                                    <x-input-error :messages="$errors->get('feedback')" class="mt-2 text-danger small" />
                                                </div>
                                            </div>

                                            <div class="col-xl-12 mt-4">
                                                <button type="submit" class="btn btn-primary">Update</button>
                                            </div>

                                        </div>
                                    </form>
                                        
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
