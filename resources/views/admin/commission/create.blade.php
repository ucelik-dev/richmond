@extends('admin.layouts.master')



@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">COMMISSION CREATE</h3>
                    <div class="card-actions">
                        <a href="{{ route('admin.commission.index') }}" class="btn btn-dark px-2 py-1 px-md-3 py-md-2">
                            <i class="fa-solid fa-arrow-left me-2"></i>
                            Back
                        </a>
                    </div>
                </div>
                <div class="card-body">

                    <div class="add_course_basic_info">

                        <form action="{{ route('admin.commission.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-xl-4">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Student Name</label>
                                        <select class="form-control form-select" name="customer_id" id="customerSelect">
                                            <option value="">Select a student</option>
                                            @foreach ($payments as $payment)
                                                <option value="{{ @$payment->user->id }}" 
                                                    data-sales="{{ @$payment->user->sales_person_id }}"
                                                    data-sales-name="{{ @$payment->user->salesPerson->name }}"
                                                    data-agent="{{ @$payment->user->agent_id }}"
                                                    data-agent-name="{{ @$payment->user->agent->name }}">
                                                    {{ @$payment->user->name }} - {{ @$payment->course->title }} ({{ @$payment->course->level->name }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" id="salesPersonId" name="sales_person_id">
                                        <input type="hidden" id="agentId" name="agent_id">
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="general_form_input">
                                        <label>Commission To <small>(User)</small></label>
                                        <select class="form-control form-select" name="user_id" id="commissionToSelect">
                                        <option value="">Select a user</option>
                                        {{-- JS will inject the sales/agent options --}}
                                        </select>
                                    </div>
                                </div>

                                <div class="col-xl-4">
                                    <div class="general_form_input">
                                        <label>Commission To <small>(External Person)</small></label>
                                        <input type="text" name="payee_name" id="externalName" class="form-control"
                                            placeholder="Type a name if payee is not a user">
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-xl-4">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Amount</label>
                                        <input type="number" name="amount" value="{{ old('amount') }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="general_form_input">
                                        <label class="label-required">Status</label>
                                        <select name="status" class="form-control form-select">
                                            <option value="">Select a payment status</option>
                                            <option value="paid">Paid</option>
                                            <option value="unpaid">Unpaid</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="general_form_input">
                                        <label>Paid at</label>
                                        <input type="date" name="paid_at" class="form-control" value="">
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <div class="general_form_input d-flex align-items-end"> {{-- Added d-flex and align-items-end --}}
                                        <div class="flex-grow-1"> {{-- Added flex-grow-1 and me-2 --}}
                                            <label>Note</label>
                                            <textarea name="note" class="form-control"></textarea>
                                        </div>
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
        document.addEventListener('DOMContentLoaded', function () {
        const customerSelect     = document.getElementById('customerSelect');
        const commissionToSelect = document.getElementById('commissionToSelect');
        const salesPersonIdInput = document.getElementById('salesPersonId');
        const agentIdInput       = document.getElementById('agentId');
        const externalNameInput  = document.getElementById('externalName');

        // Reset commissionTo options
        function resetCommissionTo() {
            commissionToSelect.innerHTML = '<option value="">Select a user</option>';
            externalNameInput.disabled = false; // default allow typing
        }

        // Enable/disable external field
        function updateExternalInputState() {
            if (commissionToSelect.value) {
            // some user selected → lock external input
            externalNameInput.disabled = true;
            externalNameInput.value = ''; // clear if any
            } else {
            // no user selected → allow typing
            externalNameInput.disabled = false;
            }
        }

        // Populate sales/agent when selecting a student
        customerSelect.addEventListener('change', function () {
            resetCommissionTo();

            const opt       = customerSelect.options[customerSelect.selectedIndex];
            const salesId   = opt.getAttribute('data-sales') || '';
            const salesName = opt.getAttribute('data-sales-name') || '';
            const agentId   = opt.getAttribute('data-agent') || '';
            const agentName = opt.getAttribute('data-agent-name') || '';

            salesPersonIdInput.value = salesId;
            agentIdInput.value       = agentId;

            if (salesId && salesName) {
            commissionToSelect.add(new Option(`${salesName} (Sales)`, salesId));
            }
            if (agentId && agentName && agentId !== salesId) {
            commissionToSelect.add(new Option(`${agentName} (Agent)`, agentId));
            }

            updateExternalInputState();
        });

        commissionToSelect.addEventListener('change', updateExternalInputState);

        updateExternalInputState(); // run on load
        });
    </script>



@endpush

