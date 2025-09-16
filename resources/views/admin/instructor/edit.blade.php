@extends('admin.layouts.master')

<style>
/* Remove Bootstrap's default arrow */
.accordion-button::after {
    display: none !important;
}
</style>

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">INSTRUCTOR UPDATE</h3>
                    <div class="card-actions">
                        <a href="{{ route('admin.instructor.index') }}" class="btn btn-default">
                            <i class="fa-solid fa-arrow-left me-2"></i>
                            Back
                        </a>
                    </div>
                </div>
                <div class="card-body">

                    <div class="add_course_basic_info">

                        <form action="{{ route('admin.instructor.update', $instructor->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="general_form_input">
                                        <label for="#">Image</label><br>
                                        <img src="{{ asset($instructor->image) }}"
                                            style="width: 150px !important; height: 150px !important; object-fit: contain !important; display: inline-block !important;">
                                        <input type="file" name="image" class="form-control mt-2">
                                        <x-input-error :messages="$errors->get('image')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                               
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Name</label>
                                        <input type="text" name="name" value="{{ $instructor->name }}" class="form-control">
                                        <x-input-error :messages="$errors->get('name')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Gender</label>
                                        <select class="form-control form-select" name="gender">
                                            <option @selected($instructor->gender == 'male') value="male">Male</option>
                                            <option @selected($instructor->gender == 'female') value="female">Female</option>
                                            <option @selected($instructor->gender == 'other') value="other">Other</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('gender')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                 <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Phone</label>
                                        <input type="text" name="phone" value="{{ $instructor->phone }}" class="form-control">
                                        <x-input-error :messages="$errors->get('phone')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Date of Birth</label>
                                        <input type="date" name="dob" value="{{ $instructor->dob }}" class="form-control">
                                        <x-input-error :messages="$errors->get('dob')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Email</label>
                                        <input type="text" name="email" value="{{ $instructor->email }}" class="form-control">
                                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#">Contact Email</label>
                                        <input type="text" name="contact_email" value="{{ $instructor->contact_email }}" class="form-control">
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
                                                <option value="{{ $country->id }}" {{ old('country_id', $instructor->country_id) == $country->id ? 'selected' : '' }}>
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
                                        <input type="text" name="city" value="{{ $instructor->city }}" class="form-control">
                                        <x-input-error :messages="$errors->get('city')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#">Post Code</label>
                                        <input type="text" name="post_code" value="{{ $instructor->post_code }}" class="form-control">
                                        <x-input-error :messages="$errors->get('post_code')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#">Address</label>
                                        <input type="text" name="address" value="{{ $instructor->address }}" class="form-control">
                                        <x-input-error :messages="$errors->get('address')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                 
                            </div>

                            <div class="row">
                               
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Account Status</label>
                                        <select class="form-control form-select" name="account_status">
                                            <option @selected($instructor->account_status == 1) value="1">Active</option>
                                            <option @selected($instructor->account_status == 0) value="0">Inactive</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('account_status')" class="mt-2 text-danger small" />
                                    </div>
                                </div>

                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">User Status</label>
                                        <select class="form-control form-select" name="user_status_id">
                                            @foreach ($userStatuses as $userStatus)
                                                <option @selected($userStatus->id === $instructor->user_status_id) value="{{ $userStatus->id }}">{{ ucwords($userStatus->name) }}</option>
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
                                                                    @foreach($instructor->documents as $index => $document)
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


                            {{-- Edit social accounts --}}

                            <div class="row">
                                      
                                <div class="col-xl-12">
                                    <div class="accordion bg-white mt-2" id="accordion-example">
                                    
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="heading-1">
                                                <button class="accordion-button collapsed bg-info text-white fw-bold py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-social" aria-expanded="false">
                                                    Social Accounts
                                                    <i class="fa-solid fa-chevron-down ms-auto"></i>
                                                </button>
                                            </h2>

                                            <div id="collapse-social" class="accordion-collapse collapse mt-4" data-bs-parent="#accordion-example" style="">
                                                <div class="accordion-body pt-0">
                                                    
                                                    <div class="row">

                                                        <div class="col-xl-12 table-responsive">
                                                            <div class="d-flex justify-content-end mb-3">
                                                                <button type="button" class="btn btn-sm btn-success px-2" id="addSocialAccountBtn">
                                                                    <i class="fa fa-plus me-1"></i><span class="fs-4">Add</span>
                                                                </button>
                                                            </div>
                                                            <table class="table table-bordered" id="socialAccountsTable">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="text-nowrap">Platform</th>
                                                                        <th class="text-nowrap">Link</th>
                                                                        <th class="text-nowrap" style="width: 30px">Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($instructor->socialAccounts ?? [] as $index => $account)
                                                                        <tr>
                                                                            <td class="align-middle">
                                                                                <div class="general_form_input mb-0">
                                                                                    <select name="social_accounts[{{ $index }}][platform_id]" class="form-control form-select">
                                                                                        <option value="">Select Platform</option>
                                                                                        @foreach ($socialPlatforms as $platform)
                                                                                            <option value="{{ $platform->id }}" @selected($platform->id == $account->social_platform_id)>
                                                                                                {{ $platform->name }}
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </td>
                                                                            <td class="align-middle">
                                                                                <div class="general_form_input mb-0">
                                                                                    <input type="url" name="social_accounts[{{ $index }}][link]" class="form-control" value="{{ $account->link }}">
                                                                                </div>
                                                                            </td>
                                                                            <td class="align-middle text-center">
                                                                                <button type="button" class="btn btn-danger remove-social-account py-1" data-id="{{ $account->id }}">Delete</button>
                                                                            </td>
                                                                        </tr>
                                                                        <input type="hidden" name="social_accounts[{{ $index }}][id]" value="{{ $account->id }}">
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
                                                                    @foreach($instructor->userNotes ?? [] as $index => $note)
                                                                        <tr>
                                                                            <td class="align-middle">
                                                                                <div class="text-secondary fs-4 general_form_input mb-0">
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
        let socialIndex = {{ $instructor->socialAccounts->count() }};

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
        let noteIndex = {{ count($instructor->userNotes ?? []) }};

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

@endpush
