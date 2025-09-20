@extends('admin.layouts.master')

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">STUDENT CREATE</h3>
                    <div class="card-actions">
                        <a href="{{ route('admin.student.index') }}" class="btn btn-default">
                            <i class="fa-solid fa-arrow-left me-2"></i>
                            Back
                        </a>
                    </div>
                </div>
                <div class="card-body">

                    <div class="add_course_basic_info">

                        <form action="{{ route('admin.student.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#">Image</label><br>
                                        <input type="file" name="image" class="form-control">
                                        <x-input-error :messages="$errors->get('image')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input"> 
                                        <label class="label-required">College</label>
                                        <select class="form-control form-select" name="college_id">
                                            <option value="">Select a college</option>
                                            @foreach($colleges as $college)
                                                <option value="{{ $college->id }}">{{ $college->name }}</option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('college_id')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Name</label>
                                        <input type="text" name="name" value="{{ old('name') }}" class="form-control">
                                        <x-input-error :messages="$errors->get('name')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Gender</label>
                                        <select class="form-control form-select" name="gender">
                                            <option value="">Select a gender</option>
                                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('gender')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                 <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Phone</label>
                                        <input type="text" name="phone" value="{{ old('phone') }}" class="form-control">
                                        <x-input-error :messages="$errors->get('phone')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Date of Birth</label>
                                        <input type="date" name="dob" value="{{ old('dob') }}" class="form-control">
                                        <x-input-error :messages="$errors->get('dob')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <div class="general_form_input">
                                        <label for="#">Education Status</label>
                                        <select class="form-control form-select" name="education_status">
                                            <option value="">Select a status</option>
                                            <option value="high_school_student" {{ old('education_status') == 'high_school_student' ? 'selected' : '' }}>High School Student</option>
                                            <option value="high_school_graduate" {{ old('education_status') == 'high_school_graduate' ? 'selected' : '' }}>High School Graduate</option>
                                            <option value="university_student" {{ old('education_status') == 'university_student' ? 'selected' : '' }}>University Student</option>
                                            <option value="university_graduate" {{ old('education_status') == 'university_graduate' ? 'selected' : '' }}>University Graduate</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('education_status')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Email</label>
                                        <input type="text" name="email" value="{{ old('email') }}" class="form-control">
                                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#">Payment Email</label>
                                        <input type="text" name="contact_email" value="{{ old('contact_email') }}" class="form-control">
                                        <x-input-error :messages="$errors->get('contact_email')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#">Country</label>
                                        <select name="country_id" class="form-control form-select">
                                            <option value="">Select a country</option>
                                            @foreach($countries as $country)
                                                <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                                    {{ $country->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('country_id')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#">City</label>
                                        <input type="text" name="city" value="{{ old('city') }}" class="form-control">
                                        <x-input-error :messages="$errors->get('city')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#">Post Code</label>
                                        <input type="text" name="post_code" value="{{ old('post_code') }}" class="form-control">
                                        <x-input-error :messages="$errors->get('post_code')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#">Address</label>
                                        <input type="text" name="address" value="{{ old('address') }}" class="form-control">
                                        <x-input-error :messages="$errors->get('address')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                 
                            </div>

                            <div class="row">
                                <div class="col-xl-3">
                                    <div class="general_form_input">
                                        <label for="#">Sales Person</label>
                                        <select class="form-control form-select" name="sales_person_id">
                                            <option value="">Select a person</option>
                                            @foreach ($salesUsers as $salesUser)
                                                    <option value="{{ $salesUser->id }}" {{ old('sales_person_id') == $salesUser->id ? 'selected' : '' }}>{{ $salesUser->name }}</option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('sales_person_id')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-3">
                                    <div class="general_form_input">
                                        <label for="#">Agent</label>
                                        <select class="form-control form-select" name="agent_id">
                                            <option value="">Select an agent</option>
                                            @foreach ($agentUsers as $agentUser)
                                                    <option value="{{ $agentUser->id }}" {{ old('agent_id') == $agentUser->id ? 'selected' : '' }}>{{ $agentUser->company }}</option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('agent_id')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-3">
                                    <div class="general_form_input">
                                        <label for="#">Manager</label>
                                        <select class="form-control form-select" name="manager_id">
                                            <option value="">Select an manager</option>
                                            @foreach ($managerUsers as $managerUser)
                                                    <option value="{{ $managerUser->id }}" {{ old('manager_id') == $managerUser->id ? 'selected' : '' }}>{{ $managerUser->name }}</option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('manager_id')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-3">
                                    <div class="general_form_input">
                                        <label for="#">Reference</label>
                                        <input type="text" name="reference" value="{{ old('reference') }}" class="form-control">
                                        <x-input-error :messages="$errors->get('reference')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                               
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Account Status</label>
                                        <select class="form-control form-select" name="account_status">
                                            <option value="1" {{ old('account_status') == '1' ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ old('account_status') == '0' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('account_status')" class="mt-2 text-danger small" />
                                    </div>
                                </div>

                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">User Status</label>
                                        <select class="form-control form-select" name="user_status_id">
                                            @foreach ($userStatuses as $userStatus)
                                                <option @selected($userStatus->name === 'pending') value="{{ $userStatus->id }}" {{ old('user_status_id') == $userStatus->id ? 'selected' : '' }}>{{ ucwords($userStatus->name) }}</option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('user_status_id')" class="mt-2 text-danger small" />
                                    </div>
                                </div>

                            </div>

                            <div class="row">

                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label class="label-required">Enter password</label>
                                        <input type="password" name="password" class="form-control" placeholder="Enter your password">
                                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label class="label-required">Confirm password</label>
                                        <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm your password">
                                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-danger small" />
                                    </div>
                                </div>

                            </div>

                            {{-- Create courses --}}

                            <div class="row">
                                      
                                <div class="col-xl-12">
                                    <div class="accordion bg-white mt-2" id="accordion-example">
                                    
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="heading-1">
                                                <button class="accordion-button collapsed bg-info text-white fw-bold py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-enrollment" aria-expanded="false">
                                                    Enrolled Courses
                                                    <i class="fa-solid fa-chevron-down ms-auto"></i>
                                                </button>
                                            </h2>

                                            <div id="collapse-enrollment" class="accordion-collapse collapse mt-4" data-bs-parent="#accordion-example" style="">
                                                <div class="accordion-body pt-0">
                                                    
                                                    <div class="row">

                                                        <div class="col-xl-12 table-responsive">
                                                            <div class="d-flex justify-content-end mb-3">
                                                                <button type="button" class="btn btn-sm btn-success px-2" id="addEnrollmentBtn">
                                                                        <i class="fa fa-plus me-2"></i><span class="fs-4">Add</span>
                                                                </button>
                                                            </div>
                                                            <table class="table table-bordered" id="enrollmentsTable">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Course</th>
                                                                        <th>Group</th>
                                                                        <th>Batch</th>
                                                                        <th>Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="enrollmentRows">
                                                                    <!-- Rows will be added dynamically -->
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

          

                            {{-- Add document --}}

                            <div class="row">
                                      
                                <div class="col-xl-12">
                                    <div class="accordion bg-white mt-2" id="accordion-example">
                                    
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="heading-1">
                                                <button class="accordion-button collapsed bg-info text-white fw-bold py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-document" aria-expanded="false">
                                                    Documents
                                                    <i class="fa-solid fa-chevron-down ms-auto"></i>
                                                </button>
                                            </h2>

                                            <div id="collapse-document" class="accordion-collapse collapse mt-4" data-bs-parent="#accordion-example" style="">
                                                <div class="accordion-body pt-0">
                                                    
                                                    <div class="row">

                                                        <div class="col-xl-12 table-responsive">
                                                            <div class="d-flex justify-content-end mb-3">
                                                                <button type="button" class="btn btn-sm btn-success px-2" id="addDocumentBtn">
                                                                    <i class="fa fa-plus me-1"></i><span class="fs-4">Add</span>
                                                                </button>
                                                            </div>
                                                            <table class="table table-bordered" id="documentsTable">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Document Category</th>
                                                                        <th>File</th>
                                                                        <th>Date</th>
                                                                        <th>Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <!-- Will be populated dynamically -->
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



                            {{-- Add note --}}

                            <div class="row">
                                      
                                <div class="col-xl-12">
                                    <div class="accordion bg-white mt-2" id="accordion-example">
                                    
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="heading-1">
                                                <button class="accordion-button collapsed bg-info text-white fw-bold py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-note" aria-expanded="false">
                                                    Notes
                                                    <i class="fa-solid fa-chevron-down ms-auto"></i>
                                                </button>
                                            </h2>

                                            <div id="collapse-note" class="accordion-collapse collapse mt-4" data-bs-parent="#accordion-example" style="">
                                                <div class="accordion-body pt-0">
                                                    
                                                    <div class="row">

                                                        <div class="col-xl-12 table-responsive">
                                                            <div class="d-flex justify-content-end mb-3">
                                                                <button type="button" class="btn btn-sm btn-success px-2" id="addNoteBtn">
                                                                    <i class="fa fa-plus me-1"></i><span class="fs-4">Add</span>
                                                                </button>
                                                            </div>
                                                            <table class="table table-bordered" id="userNotesTable">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Note</th>
                                                                        <th style="width: 30px">Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <!-- Will be populated dynamically -->
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



                            <div class="row">
                                <div class="col-xl-12 mt-3">
                                    <div class="add_course_basic_info_input">
                                        <button type="submit" class="btn btn-default mt_20">Create</button>
                                    </div>
                                </div>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')

    {{-- Add enrollment script --}}
    <script>
        let enrollmentIndex = 0;

        $('#addEnrollmentBtn').click(function () {
            $('#enrollmentRows').append(`
                <tr>
                    <td>
                        <div class="general_form_input mb-0">
                            <select name="enrollments[${enrollmentIndex}][course_id]" class="form-control">
                                <option value="">Select Course</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->title }} ({{ $course->level->name }})</option>
                                @endforeach
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="general_form_input mb-0">
                            <select name="enrollments[${enrollmentIndex}][group_id]" class="form-control">
                                <option value="">Select Group</option>
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="general_form_input mb-0">
                            <select name="enrollments[${enrollmentIndex}][batch_id]" class="form-control">
                                <option value="">Select Batch</option>
                                @foreach($batches as $batch)
                                    <option value="{{ $batch->id }}">{{ $batch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </td>
                    <td class="text-center" style="width:50px">
                        <div class="general_form_input mb-0">
                            <button type="button" class="btn btn-danger remove-enrollment py-1">Delete</button>
                        </div>
                    </td>
                </tr>
            `);
            enrollmentIndex++;
        });

        $(document).on('click', '.remove-enrollment', function () {
            $(this).closest('tr').remove();
        });
    </script>

    

    {{-- Add document script --}}
    <script>
        let documentIndex = 0;

        $('#addDocumentBtn').click(function () {
            $('#documentsTable tbody').append(`
                <tr>
                    <td>
                        <div class="general_form_input mb-0">
                            <select name="documents[${documentIndex}][category_id]" class="form-control">
                                <option value="">Select Category</option>
                                @foreach($documentCategories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="general_form_input mb-0">
                            <input type="file" name="documents[${documentIndex}][file]" class="form-control" />
                        </div>
                    </td>
                    <td>
                        <div class="general_form_input mb-0">
                            <input type="date" name="documents[${documentIndex}][date]" class="form-control" />
                        </div>
                    </td>
                    <td style="width:50px">
                        <div class="general_form_input mb-0">
                            <button type="button" class="btn btn-danger remove-document py-1">Delete</button>
                        </div>
                    </td>
                </tr>
            `);
            documentIndex++;
        });

        $(document).on('click', '.remove-document', function () {
            $(this).closest('tr').remove();
        });
    </script>

    {{-- Add social account script --}}
    <script>
        let socialIndex = 0;

        $('#addSocialAccountBtn').click(function () {
            $('#socialAccountsTable tbody').append(`
                <tr>
                    <td>
                        <div class="general_form_input mb-0">
                            <select name="social_accounts[${socialIndex}][platform_id]" class="form-control">
                                <option value="">Select Platform</option>
                                @foreach($socialPlatforms as $platform)
                                    <option value="{{ $platform->id }}">{{ $platform->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="general_form_input mb-0">
                            <input type="url" name="social_accounts[${socialIndex}][link]" class="form-control" />
                        </div>
                    </td>
                    <td class="text-center" style="width:50px">
                        <div class="general_form_input mb-0">
                            <button type="button" class="btn btn-danger remove-social-account py-1">Delete</button>
                        </div>
                    </td>
                </tr>
            `);
            socialIndex++;
        });

        $(document).on('click', '.remove-social-account', function () {
            $(this).closest('tr').remove();
        });

    </script>

    {{-- Add note script --}}
    <script>
        let noteIndex = 0;

        document.getElementById('addNoteBtn').addEventListener('click', function () {
            const table = document.querySelector('#userNotesTable tbody');

            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <textarea name="user_notes[${noteIndex}][note]" class="form-control" rows="2"></textarea>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger remove-note py-1">Delete</button>
                </td>
            `;
            table.appendChild(row);
            noteIndex++;
        });

        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-note')) {
                e.target.closest('tr').remove();
            }
        });
    </script>

@endpush
