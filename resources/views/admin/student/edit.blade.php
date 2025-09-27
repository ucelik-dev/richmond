@extends('admin.layouts.master')

@section('content')
    <div class="page-body">
        <div class="container-xl">

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">STUDENT UPDATE</h3>
                    <div class="card-actions">
                        <a href="{{ route('admin.student.index') }}" class="btn btn-default">
                            <i class="fa-solid fa-arrow-left me-2"></i>
                            Back
                        </a>
                    </div>
                </div>
                <div class="card-body">

                    <div class="add_course_basic_info">

                        <form action="{{ route('admin.student.update', $student->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            {{-- Student info --}}

                            <div class="row g-3 align-items-stretch">
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                    <label>Image</label><br>
                                    <img src="{{ asset($student->image) }}" style="width: 150px !important; height: 150px !important; object-fit: contain !important; display: inline-block !important;">
                                    <input type="file" name="image" class="form-control mt-2">
                                    <x-input-error :messages="$errors->get('image')" class="mt-2 text-danger small" />
                                    </div>
                                </div>

                                <div class="col-xl-6 d-flex flex-column">
                                    <div class="general_form_input mt-auto"> 
                                        <label class="label-required">College</label>
                                        <select class="form-control form-select" name="college_id">
                                            @foreach($colleges as $college)
                                            <option @selected($student->college_id == $college->id) value="{{ $college->id }}">{{ $college->name }}</option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('college_id')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Name</label>
                                        <input type="text" name="name" value="{{ $student->name }}" class="form-control">
                                        <x-input-error :messages="$errors->get('name')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Gender</label>
                                        <select class="form-control form-select" name="gender">
                                            <option @selected($student->gender == 'male') value="male">Male</option>
                                            <option @selected($student->gender == 'female') value="female">Female</option>
                                            <option @selected($student->gender == 'other') value="other">Other</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('gender')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                 <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Phone</label>
                                        <input type="text" name="phone" value="{{ $student->phone }}" class="form-control">
                                        <x-input-error :messages="$errors->get('phone')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Date of Birth</label>
                                        <input type="date" name="dob" value="{{ $student->dob }}" class="form-control">
                                        <x-input-error :messages="$errors->get('dob')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <div class="general_form_input">
                                        <label for="#">Education Status</label>
                                        <select class="form-control form-select" name="education_status">
                                            <option value="">Select a status</option>
                                            <option @selected($student->education_status == 'high_school_student') value="high_school_student">High School Student</option>
                                            <option @selected($student->education_status == 'high_school_graduate') value="high_school_graduate">High School Graduate</option>
                                            <option @selected($student->education_status == 'university_student') value="university_student">University Student</option>
                                            <option @selected($student->education_status == 'university_graduate') value="university_graduate">University Graduate</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('education_status')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Email</label>
                                        <input type="text" name="email" value="{{ $student->email }}" class="form-control">
                                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#">Payment Email</label>
                                        <input type="text" name="contact_email" value="{{ $student->contact_email }}" class="form-control">
                                        <x-input-error :messages="$errors->get('contact_email')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#">Country</label>
                                        <select name="country_id" class="form-control form-select">
                                            <option value="">Select a country</option>
                                            @foreach($countries as $country)
                                                <option value="{{ $country->id }}" {{ old('country_id', $student->country_id) == $country->id ? 'selected' : '' }}>
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
                                        <input type="text" name="city" value="{{ $student->city }}" class="form-control">
                                        <x-input-error :messages="$errors->get('city')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#">Post Code</label>
                                        <input type="text" name="post_code" value="{{ $student->post_code }}" class="form-control">
                                        <x-input-error :messages="$errors->get('post_code')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#">Address</label>
                                        <input type="text" name="address" value="{{ $student->address }}" class="form-control">
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
                                                    <option @selected($student->sales_person_id == $salesUser->id) value="{{ $salesUser->id }}">{{ $salesUser->name }}</option>
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
                                                <option @selected($student->agent_id == $agentUser->id) value="{{ $agentUser->id }}">{{ $agentUser->company ? $agentUser->company : $agentUser->name }}</option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('agent_id')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-3">
                                    <div class="general_form_input">
                                        <label for="#">Manager</label>
                                        <select class="form-control form-select" name="manager_id">
                                            <option value="">Select a manager</option>
                                            @foreach ($managerUsers as $managerUser)
                                                <option @selected($student->manager_id == $managerUser->id) value="{{ $managerUser->id }}">{{ $managerUser->name }}</option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('manager_id')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-3">
                                    <div class="general_form_input">
                                        <label for="#">Reference</label>
                                        <input type="text" name="reference" value="{{ $student->reference }}" class="form-control">
                                        <x-input-error :messages="$errors->get('reference')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                               
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Account Status</label>
                                        <select class="form-control form-select" name="account_status">
                                            <option @selected($student->account_status == 1) value="1">Active</option>
                                            <option @selected($student->account_status == 0) value="0">Inactive</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('account_status')" class="mt-2 text-danger small" />
                                    </div>
                                </div>

                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">User Status</label>
                                        <select class="form-control form-select" name="user_status_id">
                                            @foreach ($userStatuses as $userStatus)
                                                <option @selected($userStatus->id === $student->user_status_id) value="{{ $userStatus->id }}">{{ ucwords($userStatus->name) }}</option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('user_status_id')" class="mt-2 text-danger small" />
                                    </div>
                                </div>

                            </div>

                            <div class="row">

                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label>Enter password</label>
                                        <input type="password" name="password" class="form-control" placeholder="Enter your password">
                                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label>Confirm password</label>
                                        <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm your password">
                                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-danger small" />
                                    </div>
                                </div>

                            </div>


                            {{-- Edit courses --}}

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
                                                                <button type="button" class="btn btn-sm btn-success px-2" id="addEnrollment">
                                                                        <i class="fa fa-plus me-2"></i><span class="fs-4">Add</span>
                                                                </button>
                                                            </div>
                                                            <table class="table table-bordered">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="text-nowrap">Course</th>
                                                                        <th class="text-nowrap">Group</th>
                                                                        <th class="text-nowrap">Batch</th>
                                                                        <th class="text-nowrap" style="width: 30px">Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="enrollment-rows">
                                                                    @foreach ($student->enrollments as $index => $enrollment)
                                                                        <tr>
                                                                            <td>
                                                                                <div class="general_form_input mb-0">
                                                                                    <select name="enrollments[{{ $index }}][course_id]" class="form-control form-select" required>
                                                                                        <option value="">Select Course</option>
                                                                                        @foreach ($courses as $course)
                                                                                            <option value="{{ $course->id }}" @selected($enrollment->course_id == $course->id)>
                                                                                                {{ $course->title }} ({{ $course->level->name }})
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="general_form_input mb-0">
                                                                                    <select name="enrollments[{{ $index }}][group_id]" class="form-control form-select">
                                                                                        <option value="">Select Group</option>
                                                                                        @foreach ($groups as $group)
                                                                                            <option value="{{ $group->id }}" @selected($enrollment->group_id == $group->id)>
                                                                                                {{ $group->name }}
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="general_form_input mb-0">
                                                                                    <select name="enrollments[{{ $index }}][batch_id]" class="form-control form-select">
                                                                                        <option value="">Select Batch</option>
                                                                                        @foreach ($batches as $batch)
                                                                                            <option value="{{ $batch->id }}" @selected($enrollment->batch_id == $batch->id)>
                                                                                                {{ $batch->name }}
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </td>
                                                                            <td class="text-nowrap">
                                                                                <div class="general_form_input mb-0">
                                                                                    <button type="button" class="btn btn-danger remove-enrollment py-1">Delete</button>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <input type="hidden" name="enrollments[{{ $index }}][id]" value="{{ $enrollment->id }}">
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

                            
                            {{-- Edit awarding body --}}

                            <div class="row">
                                      
                                <div class="col-xl-12">
                                    <div class="accordion bg-white mt-2" id="accordion-example">
                                    
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="heading-1">
                                                <button class="accordion-button collapsed bg-info text-white fw-bold py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-registration" aria-expanded="false">
                                                    Awarding Body Registration
                                                    <i class="fa-solid fa-chevron-down ms-auto"></i>
                                                </button>
                                            </h2>

                                            <div id="collapse-registration" class="accordion-collapse collapse mt-4" data-bs-parent="#accordion-example" style="">
                                                <div class="accordion-body pt-0">
                                                    
                                                    <div class="row">

                                                        <div class="col-xl-12 table-responsive">
                                                            <div class="d-flex justify-content-end mb-3">
                                                                <button type="button" class="btn btn-sm btn-success px-2" id="addRegistration">
                                                                    <i class="fa fa-plus me-1"></i><span class="fs-4">Add</span>
                                                                </button>
                                                            </div>
                                                            <table class="table table-bordered" id="registrationTable">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="text-nowrap">Course</th>
                                                                        <th class="text-nowrap">Awarding Body</th>
                                                                        <th class="text-nowrap">Registration Level</th>
                                                                        <th class="text-nowrap">Registration Number</th>
                                                                        <th class="text-nowrap">Registration Date</th>
                                                                        <th class="text-nowrap" style="width: 30px">Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($student->awardingBodyRegistrations as $index => $registration)
                                                                        <tr>
                                                                            <td>
                                                                                <div class="general_form_input mb-0">
                                                                                    <select name="registrations[{{ $index }}][course_id]" class="form-control form-select">
                                                                                        <option value="">Select a course</option>
                                                                                        @foreach ($student->enrollments as $enrollment)
                                                                                            <option value="{{ $enrollment->course_id }}"
                                                                                                @selected($registration->course_id == $enrollment->course_id)>
                                                                                                {{ $enrollment->course->title }} ({{ $enrollment->course->level->name ?? '' }})
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </td>
                                                                            <td class="text-nowrap">
                                                                                <div class="general_form_input mb-0">
                                                                                    <select name="registrations[{{ $index }}][awarding_body_id]" class="form-control form-select">
                                                                                        @foreach($awardingBodies as $body)
                                                                                            <option value="{{ $body->id }}" @selected($body->id == $registration->awarding_body_id)>
                                                                                                {{ $body->name }}
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </td>
                                                                            <td class="text-nowrap">
                                                                                <div class="general_form_input mb-0">
                                                                                    <select name="registrations[{{ $index }}][awarding_body_registration_level_id]" class="form-control form-select">
                                                                                        @foreach($courseLevels as $level)
                                                                                            <option value="{{ $level->id }}" @selected($level->id == $registration->awarding_body_registration_level_id)>
                                                                                                {{ $level->name }}
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </td>
                                                                            <td class="text-nowrap">
                                                                                <div class="general_form_input mb-0">
                                                                                    <input type="text" name="registrations[{{ $index }}][awarding_body_registration_number]" value="{{ $registration->awarding_body_registration_number }}" class="form-control">
                                                                                </div>
                                                                            </td>
                                                                            <td class="text-nowrap">
                                                                                <div class="general_form_input mb-0">
                                                                                    <input type="date" name="registrations[{{ $index }}][awarding_body_registration_date]" value="{{ $registration->awarding_body_registration_date }}" class="form-control">
                                                                                </div>
                                                                            </td>
                                                                            <td class="text-nowrap">
                                                                                <div class="general_form_input mb-0">
                                                                                    <button type="button" class="btn btn-danger remove-registration py-1">Delete</button>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <input type="hidden" name="registrations[{{ $index }}][id]" value="{{ $registration->id }}">
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


                            {{-- Edit documents --}}

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
                                                            <table class="table table-bordered" id="documentTable">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Category</th>
                                                                        <th>File</th>
                                                                        <th>Date</th>
                                                                        <th style="width: 30px">Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($student->documents as $index => $document)
                                                                        <tr>
                                                                            <td class="text-secondary fs-4 text-nowrap align-middle py-1">
                                                                                {{ $document->category->name ?? 'N/A' }}
                                                                            </td>
                                                                            <td class="text-secondary fs-4 text-nowrap align-middle py-1">
                                                                                <a href="{{ asset($document->path) }}" target="_blank">{{ basename($document->path) }}</a>
                                                                            </td>
                                                                            <td class="text-secondary fs-4 text-nowrap align-middle py-1">
                                                                                {{ $document->date ? \Carbon\Carbon::parse($document->date)->format('d-m-Y') : '' }}
                                                                            </td>
                                                                            <td class="align-middle py-1">
                                                                                <button type="button" class="btn btn-danger remove-document py-1" data-id="{{ $document->id }}">Delete</button>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                            <input type="hidden" name="deleted_documents" id="deletedDocuments">
                                                        </div>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>
                            
                                    </div>
                                </div>
        
                            </div>
                            

                            {{-- Edit graduations --}}

                            <div class="row">
                                      
                                <div class="col-xl-12">
                                    <div class="accordion bg-white mt-2" id="accordion-example">
                                    
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="heading-1">
                                                <button class="accordion-button collapsed bg-info text-white fw-bold py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-graduation" aria-expanded="false">
                                                    Graduation Info
                                                    <i class="fa-solid fa-chevron-down ms-auto"></i>
                                                </button>
                                            </h2>

                                            <div id="collapse-graduation" class="accordion-collapse collapse mt-4" data-bs-parent="#accordion-example" style="">
                                                <div class="accordion-body pt-0">
                                                    
                                                    <div class="row">

                                                        <div class="col-xl-12 table-responsive">
                                                            <div class="d-flex justify-content-end mb-3">
                                                                <button type="button" class="btn btn-sm btn-success px-2" id="addGraduationBtn">
                                                                    <i class="fa fa-plus me-1"></i><span class="fs-4">Add</span>
                                                                </button>
                                                            </div>
                                                            <table class="table table-bordered" id="graduationTable">
                                                                <tbody>
                                                                    @foreach($student->graduates as $gIndex => $g)
                                                                        <tr>
                                                                            <td colspan="11">
                                                                                <div class="row g-3">

                                                                                    <div class="col-md-4">
                                                                                        <label class="form-label text-secondary label-required">Course</label>
                                                                                        <div class="general_form_input mb-0">
                                                                                            <select name="graduations[{{ $gIndex }}][course_id]" class="form-control form-select">
                                                                                                <option value="">Select a course</option>
                                                                                                @foreach ($courses->whereIn('id', $enrolledCourseIds) as $c)
                                                                                                    <option value="{{ $c->id }}" @selected($g->course_id == $c->id)>
                                                                                                        {{ $c->title }} ({{ $c->level->name ?? '—' }})
                                                                                                    </option>
                                                                                                @endforeach
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-md-4">
                                                                                        <label class="form-label text-secondary">RC Graduation Date</label>
                                                                                        <div class="general_form_input mb-0">
                                                                                            <input type="date" name="graduations[{{ $gIndex }}][rc_graduation_date]" class="form-control" value="{{ $g->rc_graduation_date }}">
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-md-4">
                                                                                        <label class="form-label text-secondary">RC Diploma File</label>
                                                                                        <div class="general_form_input mb-0">
                                                                                            <input type="file" name="graduations[{{ $gIndex }}][diploma_file]" class="form-control">
                                                                                            @if($g->diploma_file)
                                                                                                <small class="d-block mt-2">
                                                                                                    <a href="{{ asset($g->diploma_file) }}" target="_blank">View Diploma file</a>
                                                                                                </small>
                                                                                            @endif
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-md-6">
                                                                                        <label class="form-label text-secondary">Top-Up Date</label>
                                                                                        <div class="general_form_input mb-0">
                                                                                            <input type="date" name="graduations[{{ $gIndex }}][top_up_date]" class="form-control" value="{{ $g->top_up_date }}">
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-md-6">
                                                                                        <label class="form-label text-secondary">Program Entry</label>
                                                                                        <div class="general_form_input mb-0">
                                                                                            <input type="date" name="graduations[{{ $gIndex }}][program_entry_date]" class="form-control" value="{{ $g->program_entry_date }}">
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-md-4">
                                                                                        <label class="form-label text-secondary">University</label>
                                                                                        <div class="general_form_input mb-0">
                                                                                            <input type="text" name="graduations[{{ $gIndex }}][university]" class="form-control" value="{{ $g->university }}">
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-md-4">
                                                                                        <label class="form-label text-secondary">Program</label>
                                                                                        <div class="general_form_input mb-0">
                                                                                            <input type="text" name="graduations[{{ $gIndex }}][program]" class="form-control" value="{{ $g->program }}">
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-md-4">
                                                                                        <label class="form-label text-secondary">Study Mode</label>
                                                                                        <div class="general_form_input mb-0">
                                                                                            <select name="graduations[{{ $gIndex }}][study_mode]" class="form-control form-select">
                                                                                                <option value="">Select a study mode</option>
                                                                                                <option @selected($g->study_mode === 'online') value="online">Online</option>
                                                                                                <option @selected($g->study_mode === 'on_campus') value="on_campus">On Campus</option>
                                                                                                <option @selected($g->study_mode === 'hybrid') value="hybrid">Hybrid</option>
                                                                                            </select>

                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-md-4">
                                                                                        <label class="form-label text-secondary">Employed?</label>
                                                                                        <div class="general_form_input mb-0">
                                                                                            <select name="graduations[{{ $gIndex }}][job_status]" class="form-control form-select">
                                                                                                <option value="">Select</option>
                                                                                                <option value="1" {{ $g->job_status == 1 ? 'selected' : '' }}>Yes</option>
                                                                                                <option value="0" {{ $g->job_status === 0 ? 'selected' : '' }}>No</option>
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-md-4">
                                                                                        <label class="form-label text-secondary">Job Title</label>
                                                                                        <div class="general_form_input mb-0">
                                                                                            <input type="text" name="graduations[{{ $gIndex }}][job_title]" class="form-control" value="{{ $g->job_title }}">
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-md-4">
                                                                                        <label class="form-label text-secondary">Job Start</label>
                                                                                        <div class="general_form_input mb-0">
                                                                                            <input type="date" name="graduations[{{ $gIndex }}][job_start_date]" class="form-control" value="{{ $g->job_start_date }}">
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-md-12">
                                                                                        <label class="form-label text-secondary">Note</label>
                                                                                        <div class="general_form_input mb-0">
                                                                                            <textarea type="date" name="graduations[{{ $gIndex }}][note]" class="form-control" value="{{ $g->note }}"></textarea>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="col-md-12 text-end">
                                                                                        <button type="button" class="btn btn-danger remove-graduation py-1" data-id="{{ $g->id }}">Delete</button>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <input type="hidden" name="graduations[{{ $gIndex }}][id]" value="{{ $g->id }}">
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                            {{-- Track deleted graduations --}}
                                                            <input type="hidden" name="deleted_graduations" id="deletedGraduations" value="">
                                                        </div>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>
                            
                                    </div>
                                </div>
        
                            </div>


                            {{-- Edit notes --}}

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
                                                                        <th class="text-nowrap">Note</th>
                                                                        <th class="text-nowrap">Added By</th>
                                                                        <th class="text-nowrap">Created At</th>
                                                                        <th class="text-nowrap" style="width: 30px">Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($student->userNotes ?? [] as $index => $note)
                                                                        <tr>
                                                                            <td class="align-middle">
                                                                                <div class="general_form_input mb-0">
                                                                                    <textarea name="user_notes[{{ $index }}][note]" class="form-control" rows="2">{{ $note->note }}</textarea>
                                                                                </div>
                                                                            </td>
                                                                            <td class="text-secondary fs-4 align-middle">
                                                                                {{ $note->addedBy->name ?? '—' }}
                                                                            </td>
                                                                            <td class="text-secondary fs-4 align-middle">
                                                                                {{ $note->created_at->format('Y-m-d H:i') }}
                                                                            </td>
                                                                            <td class="align-middle text-center">
                                                                                <button type="button" class="btn btn-danger remove-user-note py-1" data-id="{{ $note->id }}">Delete</button>
                                                                            </td>
                                                                        </tr>
                                                                        <input type="hidden" name="user_notes[{{ $index }}][id]" value="{{ $note->id }}">
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

                            {{-- Buttons --}}

                            <div class="row">
                               
                                <div class="col-xl-12 mt-3">
                                    <div class="add_course_basic_info_input">
                                        <div class="add_course_basic_info_input d-flex gap-2">
                                            <button type="submit" name="action" value="save_exit" class="btn btn-default mt_20">Update</button>
                                            <button type="submit" name="action" value="save_stay" class="btn btn-secondary mt_20">Update & Stay</button>
                                        </div>
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

    <script>
        let enrollmentIndex = {{ $student->enrollments->count() }};

        document.getElementById('addEnrollment').addEventListener('click', function () {
            const row = `
                <tr>
                    <td>
                        <div class="general_form_input mb-0">
                            <select name="enrollments[${enrollmentIndex}][course_id]" class="form-control form-select" required>
                                <option value="">Select Course</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->title }} ({{ $course->level->name }})</option>
                                @endforeach
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="general_form_input mb-0">
                            <select name="enrollments[${enrollmentIndex}][group_id]" class="form-control form-select">
                                <option value="">Select Group</option>
                                @foreach ($groups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="general_form_input mb-0">
                            <select name="enrollments[${enrollmentIndex}][batch_id]" class="form-control form-select">
                                <option value="">Select Batch</option>
                                @foreach ($batches as $batch)
                                    <option value="{{ $batch->id }}">{{ $batch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </td>
                    <td class="text-nowrap">
                        <div class="general_form_input mb-0">
                            <button type="button" class="btn btn-danger remove-enrollment py-1">Delete</button>
                        </div>
                    </td>
                </tr>
            `;
            document.querySelector('#enrollment-rows').insertAdjacentHTML('beforeend', row);
            enrollmentIndex++;
        });

        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-enrollment')) {
                e.target.closest('tr').remove();
            }
        });
    </script>


    {{-- Edit awarding body script --}}
    <script>
        let regIndex = {{ $student->awardingBodyRegistrations->count() }};

        document.getElementById('addRegistration').addEventListener('click', function () {
            const row = `
                <tr>
                    <td>
                        <div class="general_form_input mb-0">
                            <select name="registrations[${regIndex}][course_id]" class="form-control form-select">
                                <option value="">Select a course</option>
                                @foreach ($student->enrollments as $enrollment)
                                    <option value="{{ $enrollment->course_id }}">
                                        {{ $enrollment->course->title }} ({{ $enrollment->course->level->name ?? '' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="general_form_input mb-0">
                            <select name="registrations[${regIndex}][awarding_body_id]" class="form-control form-select">
                                <option value="">Select an awarding body</option>
                                @foreach($awardingBodies as $body)
                                    <option value="{{ $body->id }}">{{ $body->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="general_form_input mb-0">
                            <select name="registrations[${regIndex}][awarding_body_registration_level_id]" class="form-control form-select">
                                <option value="">Select a level</option>
                                @foreach($courseLevels as $level)
                                    <option value="{{ $level->id }}">{{ $level->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="general_form_input mb-0">
                            <input type="text" name="registrations[${regIndex}][awarding_body_registration_number]" class="form-control" value="">
                        </div>
                    </td>
                    <td>
                        <div class="general_form_input mb-0">   
                            <input type="date" name="registrations[${regIndex}][awarding_body_registration_date]" class="form-control" value="">
                        </div>
                    </td>
                    <td><button type="button" class="btn btn-danger remove-registration py-1">Delete</button></td>
                </tr>
            `;
            document.querySelector('#registrationTable tbody').insertAdjacentHTML('beforeend', row);
            regIndex++;
        });

        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-registration')) {
                e.target.closest('tr').remove();
            }
        });
    </script>

    {{-- Edit document script --}}
    <script>
        let docIndex = 0;

        document.getElementById('addDocumentBtn').addEventListener('click', function () {
            const row = `
                <tr>
                    <td>
                        <div class="general_form_input mb-0">
                            <select name="documents[${docIndex}][category_id]" class="form-control form-select">
                                <option value="">Select a category</option>
                                @foreach ($documentCategories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="general_form_input mb-0">
                            <input type="file" name="documents[${docIndex}][file]" class="form-control">
                        </div>
                    </td>
                    <td>
                        <div class="general_form_input mb-0">
                            <input type="date" name="documents[${docIndex}][date]" class="form-control">
                        </div>
                    </td>
                    <td>
                        <div class="general_form_input mb-0">
                            <button type="button" class="btn btn-danger remove-document py-1">Delete</button>
                        </div>
                    </td>
                </tr>
            `;
            document.querySelector('#documentTable tbody').insertAdjacentHTML('beforeend', row);
            docIndex++;
        });

        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-document')) {
                e.target.closest('tr').remove();
            }
        });

        const deletedDocumentIds = [];

        document.querySelectorAll('.remove-document').forEach(button => {
            button.addEventListener('click', function () {
                const row = this.closest('tr');
                const docId = this.getAttribute('data-id');

                if (docId) {
                    deletedDocumentIds.push(docId);
                    document.getElementById('deletedDocuments').value = deletedDocumentIds.join(',');
                }

                row.remove();
            });
        });

    </script>

    {{-- Edit social account script --}}
    <script>
        let socialIndex = {{ $student->socialAccounts->count() }};

        document.getElementById('addSocialAccountBtn').addEventListener('click', function () {
            const table = document.querySelector('#socialAccountsTable tbody');
            const newRow = document.createElement('tr');

            newRow.innerHTML = `
                <td class="align-middle">
                    <div class="general_form_input mb-0">
                        <select name="social_accounts[${socialIndex}][platform_id]" class="form-control form-select">
                            <option value="">Select Platform</option>
                            @foreach ($socialPlatforms as $platform)
                                <option value="{{ $platform->id }}">{{ $platform->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </td>
                <td class="align-middle">
                    <div class="general_form_input mb-0">
                        <input type="url" name="social_accounts[${socialIndex}][link]" class="form-control">
                    </div>
                </td>
                <td class="align-middle text-center">
                    <div class="general_form_input mb-0">
                        <button type="button" class="btn btn-danger remove-social-account py-1">Delete</button>
                    </div>
                </td>
            `;

            table.appendChild(newRow);
            socialIndex++;
        });

        document.addEventListener('click', function (e) {
            if (e.target.closest('.remove-social-account')) {
                e.target.closest('tr').remove();
            }
        });
    </script>

    {{-- Edit note script --}}
    <script>
        let noteIndex = {{ count($student->userNotes ?? []) }};

        document.getElementById('addNoteBtn').addEventListener('click', function () {
            const table = document.querySelector('#userNotesTable tbody');

            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="align-middle">
                    <div class="general_form_input mb-0">
                        <textarea name="user_notes[${noteIndex}][note]" class="form-control" rows="2"></textarea>
                    </div>
                </td>
                <td class="text-secondary fs-4 align-middle">You</td>
                <td class="text-secondary fs-4 align-middle">{{ now()->format('Y-m-d H:i') }}</td>
                <td class="align-middle text-center">
                    <button type="button" class="btn btn-danger remove-user-note py-1">Delete</button>
                </td>
            `;
            table.appendChild(row);
            noteIndex++;
        });

        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-user-note')) {
                const row = e.target.closest('tr');
                row.remove();
            }
        });
    </script>

    {{-- Edit Graduations script --}}
    <script>
        let gradIndex = {{ $student->graduates->count() }};

        document.getElementById('addGraduationBtn').addEventListener('click', function () {
            const row = `
                <tr>
                    <td colspan="11">
                        <div class="row g-3">

                            <div class="col-md-4">
                                <label class="form-label text-secondary label-required">Course</label>
                                <div class="general_form_input mb-0">
                                    <select name="graduations[${gradIndex}][course_id]" class="form-control form-select">
                                        <option value="">Select a course</option>
                                        @foreach ($courses->whereIn('id', $enrolledCourseIds) as $c)
                                            <option value="{{ $c->id }}">{{ $c->title }} ({{ $c->level->name ?? '—' }})</option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-secondary">RC Graduation Date</label>
                                <div class="general_form_input mb-0">
                                    <input type="date" name="graduations[${gradIndex}][rc_graduation_date]" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-secondary">RC Diploma File</label>
                                <div class="general_form_input mb-0">
                                    <input type="file" name="graduations[${gradIndex}][diploma_file]" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-secondary">Top-Up Date</label>
                                <div class="general_form_input mb-0">
                                    <input type="date" name="graduations[${gradIndex}][top_up_date]" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-secondary">Program Entry</label>
                                <div class="general_form_input mb-0">
                                    <input type="date" name="graduations[${gradIndex}][program_entry_date]" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-secondary">University</label>
                                <div class="general_form_input mb-0">
                                    <input type="text" name="graduations[${gradIndex}][university]" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-secondary">Program</label>
                                <div class="general_form_input mb-0">
                                    <input type="text" name="graduations[${gradIndex}][program]" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-secondary">Study Mode</label>
                                <div class="general_form_input mb-0">
                                    <select name="graduations[${gradIndex}][study_mode]" class="form-control form-select">
                                        <option value="">Select a study mode</option>
                                        <option value="online">Online</option>
                                        <option value="on_campus">On Campus</option>
                                        <option value="hybrid">Hybrid</option>
                                    </select>

                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-secondary">Employed?</label>
                                <div class="general_form_input mb-0">
                                    <select name="graduations[${gradIndex}][job_status]" class="form-control form-select">
                                        <option value="">Select</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-secondary">Job Title</label>
                                <div class="general_form_input mb-0">
                                    <input type="text" name="graduations[${gradIndex}][job_title]" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-secondary">Job Start</label>
                                <div class="general_form_input mb-0">
                                    <input type="date" name="graduations[${gradIndex}][job_start_date]" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-12 text-end">
                                <button type="button" class="btn btn-danger remove-graduation py-1">Delete</button>
                            </div>

                        </div>
                    </td>
                </tr>
            `;
            document.querySelector('#graduationTable tbody').insertAdjacentHTML('beforeend', row);
            gradIndex++;
        });

        // delete row (same style as others)
        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-graduation')) {
                e.target.closest('tr').remove();
            }
        });
    </script>



@endpush
