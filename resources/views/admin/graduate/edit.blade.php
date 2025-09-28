@extends('admin.layouts.master')

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">GRADUATE UPDATE</h3>
                    <div class="card-actions">
                        <a href="{{ route('admin.graduate.index') }}" class="btn btn-dark px-2 py-1 px-md-3 py-md-2">
                            <i class="fa-solid fa-arrow-left me-2"></i>
                            Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="add_course_basic_info">
                        <form action="{{ route('admin.graduate.update', $graduate->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                 <div class="col-md-6">
                                    <div class="general_form_input">
                                        <label class="form-label label-required text-secondary">Student Name</label>
                                        <input type="text" class="form-control" value="{{ $graduate->user?->name }}" disabled>
                                        <input type="hidden" name="user_id" value="{{ $graduate->user_id }}">
                                    </div>
                                    <x-input-error :messages="$errors->get('user_id')" class="mt-2 text-danger small" />
                                </div>
                                <div class="col-md-6">
                                    <div class="general_form_input">
                                        <label class="form-label label-required text-secondary">Course Name</label>
                                        <select name="course_id" id="courseSelect" class="form-control form-select">
                                        <option value="">Select a course</option>
                                        @foreach(($graduate->user->enrollments ?? []) as $enrollment)
                                            @if($enrollment->course)
                                            <option value="{{ $enrollment->course->id }}"
                                                    @selected(old('course_id', $graduate->course_id) == $enrollment->course->id)>
                                                {{ $enrollment->course->title }} ({{ $enrollment->course->level->name ?? '—' }})
                                            </option>
                                            @endif
                                        @endforeach
                                        </select>
                                    </div>
                                    <x-input-error :messages="$errors->get('course_id')" class="mt-2 text-danger small" />
                                </div>
                                <div class="col-md-6">
                                    <div class="general_form_input ">
                                        <label class="form-label text-secondary">RC Graduation Date</label>
                                        <input type="date" name="rc_graduation_date" class="form-control" value="{{ $graduate->rc_graduation_date }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="general_form_input ">
                                        <label class="form-label text-secondary">RC Diploma File 
                                            @if($graduate->diploma_file)
                                                <small class="">
                                                    (<a href="{{ asset($graduate->diploma_file) }}" target="_blank">View current file</a>)
                                                </small>
                                            @endif
                                        </label>
                                        <input type="file" name="diploma_file" class="form-control">
                                        
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="general_form_input ">
                                        <label class="form-label text-secondary">Top-Up Date</label>
                                        <input type="date" name="top_up_date" class="form-control" value="{{ $graduate->top_up_date }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="general_form_input ">
                                        <label class="form-label text-secondary">Program Entry Date</label>
                                        <input type="date" name="program_entry_date" class="form-control" value="{{ $graduate->program_entry_date }}">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="general_form_input ">
                                        <label class="form-label text-secondary">University Name</label>
                                        <input type="text" name="university" class="form-control" value="{{ $graduate->university }}">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="general_form_input ">
                                        <label class="form-label text-secondary">Program Name</label>
                                        <input type="text" name="program" class="form-control" value="{{ $graduate->program }}">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="general_form_input ">
                                        <label class="form-label text-secondary">Study Mode</label>
                                        <select name="study_mode" class="form-control form-select">
                                            <option value="">Select a study mode</option>
                                            <option @selected($graduate->study_mode === 'online') value="online">Online</option>
                                            <option @selected($graduate->study_mode === 'on_campus') value="on_campus">On Campus</option>
                                            <option @selected($graduate->study_mode === 'hybrid') value="hybrid">Hybrid</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="general_form_input ">
                                        <label class="form-label text-secondary">Employed?</label>
                                        <select name="job_status" class="form-control form-select">
                                            <option value="">Select</option>
                                            <option @selected($graduate->job_status === 1) value="1">Yes</option>
                                            <option @selected($graduate->job_status === 0) value="0">No</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="general_form_input">
                                        <label class="form-label text-secondary">Job Title</label>
                                        <input type="text" name="job_title" class="form-control" value="{{ $graduate->job_title }}">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="general_form_input">
                                        <label class="form-label text-secondary">Job Start Name</label>
                                        <input type="date" name="job_start_date" class="form-control" value="{{ $graduate->job_start_date }}">
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="general_form_input">
                                        <label class="form-label text-secondary">Note</label>
                                        <textarea type="date" name="note" class="form-control">{{ $graduate->note }}</textarea>
                                    </div>
                                </div>

                            </div>

                            <button type="submit" class="btn btn-dark px-2 py-1 px-md-3 py-md-2 mt-2">Update</button>
                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection