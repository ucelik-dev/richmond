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
                    <h3 class="card-title">AGENT UPDATE</h3>
                    <div class="card-actions">
                        <a href="{{ route('admin.agent.index') }}" class="btn btn-dark px-2 py-1 px-md-3 py-md-2">
                            <i class="fa-solid fa-arrow-left me-2"></i>
                            Back
                        </a>
                    </div>
                </div>
                <div class="card-body">

                    <div class="add_course_basic_info">

                        <form action="{{ route('admin.agent.update', $agent->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row g-3 align-items-stretch">
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                    <label>Image</label><br>
                                    <img src="{{ asset($agent->image) }}" style="width: 150px !important; height: 150px !important; object-fit: contain !important; display: inline-block !important;">
                                    <input type="file" name="image" class="form-control mt-2">
                                    <x-input-error :messages="$errors->get('image')" class="mt-2 text-danger small" />
                                    </div>
                                </div>

                                <div class="col-xl-6 d-flex flex-column">
                                    <div class="general_form_input mt-auto"> 
                                        <label class="label-required">College</label>
                                        <select class="form-control form-select" name="college_id">
                                            @foreach($colleges as $college)
                                            <option @selected($agent->college_id == $college->id) value="{{ $college->id }}">{{ $college->name }}</option>
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
                                        <input type="text" name="name" value="{{ $agent->name }}" class="form-control">
                                        <x-input-error :messages="$errors->get('name')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#">Company</label>
                                        <input type="text" name="company" value="{{ $agent->company }}" class="form-control">
                                        <x-input-error :messages="$errors->get('company')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                 <div class="col-xl-12">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Phone</label>
                                        <input type="text" name="phone" value="{{ $agent->phone }}" class="form-control">
                                        <x-input-error :messages="$errors->get('phone')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Email</label>
                                        <input type="text" name="email" value="{{ $agent->email }}" class="form-control">
                                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#">Contact Email</label>
                                        <input type="text" name="contact_email" value="{{ $agent->contact_email }}" class="form-control">
                                        <x-input-error :messages="$errors->get('contact_email')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xl-4">
                                    <div class="general_form_input">
                                        <label for="#">Country</label>
                                        <select name="country_id" class="form-control form-select">
                                            <option value="">Select a country</option>
                                            @foreach($countries as $country)
                                                <option value="{{ $country->id }}" {{ old('country_id', $agent->country_id) == $country->id ? 'selected' : '' }}>
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
                                        <input type="text" name="city" value="{{ $agent->city }}" class="form-control">
                                        <x-input-error :messages="$errors->get('city')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="general_form_input">
                                        <label for="#">Post Code</label>
                                        <input type="text" name="post_code" value="{{ $agent->post_code }}" class="form-control">
                                        <x-input-error :messages="$errors->get('post_code')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <div class="general_form_input">
                                        <label for="#">Address</label>
                                        <input type="text" name="address" value="{{ $agent->address }}" class="form-control">
                                        <x-input-error :messages="$errors->get('address')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                 
                            </div>

                            <div class="row">
                               
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Account Status</label>
                                        <select class="form-control form-select" name="account_status">
                                            <option @selected($agent->account_status == 1) value="1">Active</option>
                                            <option @selected($agent->account_status == 0) value="0">Inactive</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('account_status')" class="mt-2 text-danger small" />
                                    </div>
                                </div>

                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">User Status</label>
                                        <select class="form-control form-select" name="user_status_id">
                                            @foreach ($userStatuses as $userStatus)
                                                <option @selected($userStatus->id === $agent->user_status_id) value="{{ $userStatus->id }}">{{ ucwords($userStatus->name) }}</option>
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

                            @if($isAgent)

                            {{-- Edit commission --}}

                                <div class="mt-3">
                                    <span class="fw-bold fs-3">Commission & Discount</span>
                                    <hr class="mt-1 mb-3">
                                </div>

                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="general_form_input">
                                            <label class="label-required">Agent Code</label>
                                            <input type="text" name="agent_code" value="{{ @$agent->agentProfile->agent_code }}" class="form-control">
                                            <x-input-error :messages="$errors->get('commission_percent')" class="mt-2 text-danger small" />
                                        </div>
                                    </div>
                                    <div class="col-xl-3">
                                        <div class="general_form_input">
                                            <label for="#">Commission Percent (%)</label>
                                            <input type="number" name="commission_percent" value="{{ @$agent->agentProfile->commission_percent }}" class="form-control">
                                            <x-input-error :messages="$errors->get('commission_percent')" class="mt-2 text-danger small" />
                                        </div>
                                    </div>
                                    <div class="col-xl-3">
                                        <div class="general_form_input">
                                            <label for="#">Commission Amount (£)</label>
                                            <input type="number" name="commission_amount" value="{{ @$agent->agentProfile->commission_amount }}" class="form-control">
                                            <x-input-error :messages="$errors->get('commission_amount')" class="mt-2 text-danger small" />
                                        </div>
                                    </div>
                                    <div class="col-xl-3">
                                        <div class="general_form_input">
                                            <label for="#">Discount Percent (%)</label>
                                            <input type="number" name="discount_percent" value="{{ @$agent->agentProfile->discount_percent }}" class="form-control">
                                            <x-input-error :messages="$errors->get('discount_percent')" class="mt-2 text-danger small" />
                                        </div>
                                    </div>
                                    <div class="col-xl-3">
                                        <div class="general_form_input">
                                            <label for="#">Discount Amount (£)</label>
                                            <input type="number" name="discount_amount" value="{{ @$agent->agentProfile->discount_amount }}" class="form-control">
                                            <x-input-error :messages="$errors->get('discount_amount')" class="mt-2 text-danger small" />
                                        </div>
                                    </div>
                                </div>

                            @endif

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
                                                                    @foreach($agent->documents as $index => $document)
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
                                                                    @foreach($agent->socialAccounts ?? [] as $index => $account)
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
                                                                    @foreach($agent->userNotes ?? [] as $index => $note)
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


                            <div class="row">
                               
                                <div class="col-xl-12">
                                    <div class="add_course_basic_info_input">
                                        <div class="add_course_basic_info_input d-flex gap-2">
                                            <button type="submit" name="action" value="save_exit" class="btn btn-dark px-2 py-1 px-md-3 py-md-2 mt-4">Update</button>
                                            <button type="submit" name="action" value="save_stay" class="btn btn-secondary px-2 py-1 px-md-3 py-md-2 mt-4">Update & Stay</button>
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
        let socialIndex = {{ $agent->socialAccounts->count() }};

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
        let noteIndex = {{ count($agent->userNotes ?? []) }};

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
