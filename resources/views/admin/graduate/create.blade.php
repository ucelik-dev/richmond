@extends('admin.layouts.master')

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">GRADUATE CREATE</h3>
                    <div class="card-actions">
                        <a href="{{ route('admin.graduate.index') }}" class="btn btn-dark px-2 py-1 px-md-3 py-md-2">
                            <i class="fa-solid fa-arrow-left me-2"></i>
                            Back
                        </a>
                    </div>
                </div>
                <div class="card-body">

                    <div class="add_course_basic_info">

                        <form action="{{ route('admin.graduate.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Student Name</label>
                                        <select name="user_id" id="studentSelect" class="form-control form-select">
                                            <option value="">Select a student</option>
                                            @foreach($students as $student)
                                                <option value="{{ $student->id }}" @selected(old('user_id')==$student->id)>{{ $student->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Course Name</label>
                                        <select name="course_id" id="courseSelect" class="form-control form-select" {{ old('course_id') ? '' : 'disabled' }}>
                                            <option value="">Select a course</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="general_form_input ">
                                        <label class="form-label text-secondary">RC Graduation Date</label>
                                        <input type="date" name="rc_graduation_date" class="form-control" value="{{ old('rc_graduation_date') }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="general_form_input ">
                                        <label class="form-label text-secondary">RC Diploma File</label>
                                        <input type="file" name="diploma_file" class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="general_form_input ">
                                        <label class="form-label text-secondary">Top-Up Date</label>
                                        <input type="date" name="top_up_date" class="form-control" value="{{ old('top_up_date') }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="general_form_input ">
                                        <label class="form-label text-secondary">Program Entry Date</label>
                                        <input type="date" name="program_entry_date" class="form-control" value="{{ old('program_entry_date') }}">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="general_form_input ">
                                        <label class="form-label text-secondary">University Name</label>
                                        <input type="text" name="university" class="form-control" value="{{ old('university') }}">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="general_form_input ">
                                        <label class="form-label text-secondary">Program Name</label>
                                        <input type="text" name="program" class="form-control" value="{{ old('program') }}">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="general_form_input ">
                                        <label class="form-label text-secondary">Study Mode</label>
                                        <select name="study_mode" class="form-control form-select">
                                            <option value="">Select a study mode</option>
                                            <option value="online">Online</option>
                                            <option value="on_campus">On Campus</option>
                                            <option value="hybrid">Hybrid</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="general_form_input ">
                                        <label class="form-label text-secondary">Employed?</label>
                                        <select name="job_status" class="form-control form-select">
                                            <option value="">Select</option>
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="general_form_input">
                                        <label class="form-label text-secondary">Job Title</label>
                                        <input type="text" name="job_title" class="form-control" value="{{ old('job_title') }}">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="general_form_input">
                                        <label class="form-label text-secondary">Job Start Name</label>
                                        <input type="date" name="job_start_date" class="form-control" value="{{ old('job_start_date') }}">
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="general_form_input">
                                        <label class="form-label text-secondary">Note</label>
                                        <textarea type="date" name="note" class="form-control" value="{{ old('note') }}"></textarea>
                                    </div>
                                </div>

                            </div>

                            <button type="submit" class="btn btn-dark px-2 py-1 px-md-3 py-md-2 mt-2">Create</button>
        
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')

    <script>
        // Build: studentId -> [{id, label}]
        const studentCourses = {
            @foreach($students as $stu)
            "{{ $stu->id }}": [
                @foreach($stu->enrollments as $en)
                @if($en->course)
                    { id: "{{ $en->course->id }}", label: @json($en->course->title . ' (' . ($en->course->level->name ?? '—') . ')') },
                @endif
                @endforeach
            ],
            @endforeach
        };

        const studentSelect = document.getElementById('studentSelect');
        const courseSelect  = document.getElementById('courseSelect');

        function fillCourses(studentId, preselect = null) {
            const list = studentCourses[studentId] || [];
            courseSelect.innerHTML = '<option value="">Select a course</option>';

            if (!list.length) {
            courseSelect.disabled = true;
            return;
            }

            list.forEach(c => {
            const opt = document.createElement('option');
            opt.value = c.id;
            opt.textContent = c.label;
            if (preselect && String(preselect) === String(c.id)) opt.selected = true;
            courseSelect.appendChild(opt);
            });
            courseSelect.disabled = false;
        }

        studentSelect.addEventListener('change', function () {
            fillCourses(this.value, null);
        });

        // Restore after validation error (if any)
        @if(old('user_id'))
            fillCourses(@json(old('user_id')), @json(old('course_id')));
        @endif
    </script>


@endpush

