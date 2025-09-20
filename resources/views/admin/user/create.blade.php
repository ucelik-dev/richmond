@extends('admin.layouts.master')

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">USER CREATE</h3>
                    <div class="card-actions">
                        <a href="{{ route('admin.user.index') }}" class="btn btn-default">
                            <i class="fa-solid fa-arrow-left me-2"></i>
                            Back
                        </a>
                    </div>
                </div>
                <div class="card-body">

                    <div class="add_course_basic_info">

                        <form action="{{ route('admin.user.store') }}" method="POST" enctype="multipart/form-data">
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
                                        <label for="#" class="label-required">Roles</label>
                                        <select class="form-control select2-multiple" name="roles[]" multiple>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }}" {{ in_array($role->id, old('roles', [])) ? 'selected' : '' }}>
                                                    {{ ucwords($role->name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('roles')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Main Role</label>
                                        <select class="form-control form-select" name="main_role_id">
                                            <option value="">Select a role</option>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }}" {{ old('main_role_id') == $role->id ? 'selected' : '' }}>
                                                    {{ ucwords($role->name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('main_role_id')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#">Batch</label>
                                        <select class="form-control form-select" name="batch_id">
                                            <option value="">Select a batch</option>
                                            @foreach ($batches as $batch)
                                                <option value="{{ $batch->id }}" {{ old('batch_id') == $batch->id ? 'selected' : '' }}>
                                                    {{ $batch->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('batch_id')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#">Group</label>
                                        <select class="form-control form-select" name="group_id">
                                            <option value="">Select a group</option>
                                            @foreach ($groups as $group)
                                                <option value="{{ $group->id }}" {{ old('group_id') == $group->id ? 'selected' : '' }}>
                                                    {{ $group->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('group_id')" class="mt-2 text-danger small" />
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

                            {{-- Add address --}}

                            <div class="mt-3">
                                <span class="fw-bold fs-3">Address</span>
                                <hr class="mt-1 mb-3">
                            </div>

                            <div class="row">
                                <div class="col-xl-4">
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
                                <div class="col-xl-4">
                                    <div class="general_form_input">
                                        <label for="#">City</label>
                                        <input type="text" name="city" value="{{ old('city') }}" class="form-control">
                                        <x-input-error :messages="$errors->get('city')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-4">
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
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#">Company</label>
                                        <input type="text" name="company" value="{{ old('company') }}" class="form-control">
                                        <x-input-error :messages="$errors->get('company')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                 
                            </div>

                            {{-- Create courses --}}

                            <div class="mt-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold fs-3">Enrolled Courses</span>
                                    <button type="button" class="btn btn-sm btn-primary" id="addEnrollmentBtn">
                                        <i class="fa fa-plus me-2"></i> Add Enrollment
                                    </button>
                                </div>
                                <hr class="mt-1 mb-3">
                            </div>

                            <div class="row">
                                <div class="col-xl-12 table-responsive">
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

                            {{-- Add registration --}}

                            <div class="mt-5">
                                <span class="fw-bold fs-3">Registration</span>
                                <hr class="mt-1 mb-3">
                            </div>

                            <div class="row">
                                <div class="col-xl-4">
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
                                <div class="col-xl-4">
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
                                <div class="col-xl-4">
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
                            </div>

                            {{-- Add commission --}}

                            <div class="mt-3">
                                <span class="fw-bold fs-3">Commission</span>
                                <hr class="mt-1 mb-3">
                            </div>

                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#">Percent (%)</label>
                                            <input type="number" name="commission_percent" value="{{ old('commission_percent', 0) }}" class="form-control">
                                        <x-input-error :messages="$errors->get('commission_percent')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#">Amount (£)</label>
                                            <input type="number" name="commission_amount" value="{{ old('commission_amount', 0) }}" class="form-control">
                                        <x-input-error :messages="$errors->get('commission_amount')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                            </div>

                            {{-- Add document --}}

                            <div class="mt-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold fs-3">Documents</span>
                                    <button type="button" class="btn btn-sm btn-primary" id="addDocumentBtn">
                                        <i class="fa fa-plus me-2"></i> Add Document
                                    </button>
                                </div>
                                <hr class="mt-1 mb-3">
                            </div>

                            <div class="row">
                                <div class="col-xl-12 table-responsive">
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

                            {{-- Add social account --}}

                            <div class="mt-5">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold fs-3">Social Accounts</span>
                                    <button type="button" class="btn btn-sm btn-primary mt-2" id="addSocialAccountBtn">+ Add Social Account</button>
                                </div>
                                <hr class="mt-1 mb-3">
                            </div>

                            <div class="row">
                                <div class="col-xl-12 table-responsive">
                                    <table class="table table-bordered" id="socialAccountsTable">
                                        <thead>
                                            <tr>
                                                <th>Platform</th>
                                                <th>Link</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Will be populated dynamically -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Add note --}}

                            <div class="mt-5">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold fs-3">Notes</span>
                                    <button type="button" class="btn btn-sm btn-primary mt-2" id="addNoteBtn">+ Add Note</button>
                                </div>
                                <hr class="mt-1 mb-3">
                            </div>

                            <div class="row">
                                <div class="col-xl-12 table-responsive">
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

                            {{-- Add status --}}

                            <div class="mt-5">
                                <span class="fw-bold fs-3">Status</span>
                                <hr class="mt-1 mb-3">
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

                            {{-- Add password --}}

                            <div class="row">

                                <div class="mt-3">
                                    <span class="fw-bold fs-3">Password</span>
                                    <hr class="mt-1 mb-3">
                                </div>

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