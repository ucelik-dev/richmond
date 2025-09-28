@extends('admin.layouts.master')

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">RECRUITMENT UPDATE</h3>
                    <div class="card-actions">
                        <a href="{{ route('admin.recruitment.index') }}" class="btn btn-dark px-2 py-1 px-md-3 py-md-2">
                            <i class="fa-solid fa-arrow-left me-2"></i>
                            Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="add_course_basic_info">
                        <form action="{{ route('admin.recruitment.update', $recruitment->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">

                                <div class="col-xl-4">
                                    <div class="general_form_input">
                                        <label class="label-required">Name</label>
                                        <input type="text" name="name" value="{{ $recruitment->name }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="general_form_input">
                                        <label class="label-required">Phone</label>
                                        <input type="text" name="phone" value="{{ $recruitment->phone }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="general_form_input">
                                        <label>Email</label>
                                        <input type="text" name="email" value="{{ $recruitment->email }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="general_form_input">
                                        <label>Country</label>
                                        <select class="form-control form-select" name="country_id">
                                            <option value=""> Please Select </option>
                                            @foreach ($countries as $country)
                                                <option @selected($recruitment->country_id === $country->id) value="{{ $country->id }}"> {{ $country->name }} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="general_form_input">
                                        <label class="label-required">Source</label>
                                        <select class="form-control form-select" name="source_id">
                                            <option value=""> Please Select </option>
                                            @foreach ($recruitmentSources as $recruitmentSource)
                                                <option @selected($recruitment->source_id === $recruitmentSource->id) value="{{ $recruitmentSource->id }}"> {{ $recruitmentSource->name }} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-xl-4">
                                    <div class="general_form_input">
                                        <label class="label-required">Status</label>
                                        <select class="form-control form-select" name="status_id">
                                            <option value=""> Please Select </option>
                                            @foreach ($recruitmentStatuses as $recruitmentStatus)
                                                <option @selected($recruitment->status_id === $recruitmentStatus->id) value="{{ $recruitmentStatus->id }}"> {{ $recruitmentStatus->name }} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                            </div>

                            {{-- Edit call logs --}}
                            
                            <div class="mt-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold fs-3">Call Logs</span>
                                    <button type="button" class="btn btn-sm btn-primary" id="addCallBtn">
                                        <i class="fa fa-plus me-2"></i> Add Call Log
                                    </button>
                                </div>
                                <hr class="mt-1 mb-3">
                            </div>

                            <div class="row">
                                <div class="col-xl-12 table-responsive">
                                    <table class="table table-bordered" id="callTable">
                                        <thead>
                                            <tr>
                                                <th>Info</th>
                                                <th>Note</th>
                                                <th style="width: 30px">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recruitment->callLogs as $index => $callLog)
                                                <tr>
                                                    <td class="text-nowrap align-middle py-1" style="width: 500px">
                                                        <div class="general_form_input mb-2 d-flex align-items-center" style="min-width: 350px;max-width: 500px;">
                                                            <label class="label-required me-2" style="min-width: 120px;">Communication</label>
                                                            <select name="call_logs[{{ $index }}][communication_method]" class="form-control form-select">
                                                                <option value="">Select</option>
                                                                <option @selected($callLog->communication_method === 'call') value="call">Call</option>
                                                                <option @selected($callLog->communication_method === 'message') value="message">Message</option>
                                                            </select>
                                                        </div>
                                                        <div class="general_form_input mb-1 d-flex align-items-center" style="min-width: 350px;max-width: 500px;">
                                                            <label class="label-required me-2" style="min-width: 120px;">Status</label>
                                                            <select name="call_logs[{{ $index }}][call_status_id]" class="form-control form-select">
                                                                <option value="">Select</option>
                                                                @foreach($recruitmentStatuses as $status)
                                                                    <option @selected($status->id === $callLog->status_id) value="{{ $status->id }}">{{ $status->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <input type="hidden" name="call_logs[{{ $index }}][existing_id]" value="{{ $callLog->id }}">
                                                    </td>

                                                    <td class="text-nowrap align-middle py-1">
                                                        <div class="general_form_input mb-2 mt-1">
                                                            <textarea name="call_logs[{{ $index }}][note]" class="form-control" rows="3">{{ $callLog->note }}</textarea>
                                                        </div>
                                                        <div class="general_form_input mb-1">
                                                            <span class="text-secondary">{{ $callLog->caller->name }}</span> : <span class="text-secondary">{{ $callLog->created_at }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="align-middle py-1">
                                                        <button type="button" class="btn btn-danger remove-call py-1" data-id="{{ $callLog->id }}">Delete</button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <input type="hidden" name="deleted_calls" id="deletedCalls">
                                </div>
                            </div>

                            <div class="row">
                               
                                <div class="col-xl-12">
                                    <div class="add_course_basic_info_input">
                                        <div class="add_course_basic_info_input d-flex gap-2">
                                            <button type="submit" name="action" value="save_exit" class="btn btn-dark px-2 py-1 px-md-3 py-md-2 mt-2">Update</button>
                                            <button type="submit" name="action" value="save_stay" class="btn btn-secondary px-2 py-1 px-md-3 py-md-2 mt-2">Update & Stay</button>
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
        let callIndex = {{ $recruitment->callLogs->count() }};

        document.getElementById('addCallBtn').addEventListener('click', function () {
            const row = `
                <tr>
                    <td class="text-nowrap align-middle py-1" style="width: 500px">
                        <div class="general_form_input mb-2 mt-1 d-flex align-items-center" style="min-width: 350px;max-width: 500px;">
                            <label class="label-required me-2" style="min-width: 120px;">Communication</label>
                            <select name="call_logs[${callIndex}][communication_method]" class="form-control">
                                <option value="">Select</option>
                                <option value="call">Call</option>
                                <option value="message">Message</option>
                            </select>
                        </div>
                        <div class="general_form_input mb-1 d-flex align-items-center" style="min-width: 350px;max-width: 500px;">
                            <label class="label-required me-2" style="min-width: 120px;">Status</label>
                            <select name="call_logs[${callIndex}][call_status_id]" class="form-control form-select">
                                <option value="">Select</option>
                                @foreach($recruitmentStatuses as $status)
                                    <option value="{{ $status->id }}">{{ $status->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </td>
                    <td class="text-nowrap align-middle py-1">
                        <div class="general_form_input mb-0">
                            <textarea name="call_logs[${callIndex}][note]" class="form-control" rows="3"></textarea>
                        </div>
                    </td>
                    <td>
                        <div class="general_form_input mb-0">
                            <button type="button" class="btn btn-danger remove-call py-1">Delete</button>
                        </div>
                    </td>
                </tr>
            `;
            document.querySelector('#callTable tbody').insertAdjacentHTML('beforeend', row);
            callIndex++;
        });

        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-call')) {
                e.target.closest('tr').remove();
            }
        });

        const deletedCallIds = [];

        document.querySelectorAll('.remove-call').forEach(button => {
            button.addEventListener('click', function () {
                const row = this.closest('tr');
                const callId = this.getAttribute('data-id');

                if (callId) {
                    deletedCallIds.push(callId);
                    document.getElementById('deletedCalls').value = deletedCallIds.join(',');
                }

                row.remove();
            });
        });

    </script>
@endpush