@extends('admin.layouts.master')

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">PAYMENT UPDATE</h3>
                    <div class="card-actions">
                        <a href="{{ route('admin.payment.index') }}" class="btn btn-dark px-2 py-1 px-md-3 py-md-2">
                            <i class="fa-solid fa-arrow-left me-2"></i>
                            Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="add_course_basic_info">
                        <form action="{{ route('admin.payment.update', $payment->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-xl-12 text-end"><!-- right-align everything inside -->
                                    <small class="d-block">Created : {{ \Carbon\Carbon::parse($payment->created_at)->format('d-m-Y') }}</small>
                                    <small class="d-block">Updated : {{ \Carbon\Carbon::parse($payment->updated_at)->format('d-m-Y') }}</small>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-4">
                                    <div class="general_form_input">
                                        <label>Name</label>
                                        <input type="text" class="form-control" value="{{ $payment->user->name }}" disabled>
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="general_form_input">
                                        <label>Email</label>
                                        <input type="text" class="form-control" value="{{ $payment->user->email }}" disabled>
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="general_form_input">
                                        <label>Phone</label>
                                        <input type="text" class="form-control" value="{{ $payment->user->phone }}" disabled>
                                    </div>
                                </div>
                                <div class="col-xl-3">
                                    <div class="general_form_input">
                                        <label>Course</label>
                                        <input type="text" class="form-control" value="{{ $payment->course->title }} ({{ $payment->course->level->name }})" disabled>
                                    </div>
                                </div>
                                <div class="col-xl-3">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Status</label>
                                        <select class="form-control form-select" name="status_id">
                                            <option value=""> Please Select </option>
                                            @foreach ($paymentStatuses as $paymentStatus)
                                                <option @selected($payment->status_id === $paymentStatus->id) value="{{ $paymentStatus->id }}"> {{ ucwords($paymentStatus->name) }} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-2">
                                    <div class="general_form_input">
                                        <label class="label-required">Amount</label>
                                        <input type="number" id="amount" name="amount" class="form-control" value="{{ $payment->amount }}">
                                        <x-input-error :messages="$errors->get('amount')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-2">
                                    <div class="general_form_input">
                                        <label>Discount</label>
                                        <input type="number" id="discount" name="discount" class="form-control" value="{{ $payment->discount }}">
                                    </div>
                                </div>
                                <div class="col-xl-2">
                                    <div class="general_form_input">
                                        <label>Total</label>
                                        <input type="number" id="total" name="total" class="form-control" value="{{ $payment->amount - $payment->discount }}" readonly>
                                        <x-input-error :messages="$errors->get('total')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <div class="general_form_input">
                                        <label>Notes</label>
                                        <textarea rows="4" name="notes" class="form-control">{{ $payment->notes }}</textarea>
                                        <x-input-error :messages="$errors->get('notes')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold">Installments</span>
                                    <button type="button" class="btn btn-sm btn-primary" id="addInstallment">
                                        <i class="fa fa-plus me-2"></i> Add Installment
                                    </button>
                                </div>
                                <hr class="mt-1 mb-3">
                            </div>

                            <div id="installmentsContainer">
                                @foreach ($installments as $index => $installment)
                                    <div class="row installment-row mb-2 align-items-center" data-id="{{ $installment->id }}" data-status="{{ $installment->status }}" data-locked="{{ $installment->status == 'paid' ? 'true' : 'false' }}" {{ $installment->status == 'paid' ? 'bg-success-subtle' : ($installment->status == 'failed' ? 'bg-danger-subtle' : 'bg-warning-subtle') }}">
                                        <div class="col-xl-1" style="width: 50px">
                                            <div class="general_form_input">
                                                <label class="form-label">&nbsp;</label>
                                                <p># {{ $index+1 }}</p>
                                            </div>
                                        </div>
                                        <div class="col-xl-2">
                                            <div class="general_form_input">
                                                <label class="form-label">Due Date</label>
                                                <input type="date" name="installments[{{ $index }}][due_date]" class="form-control" value="{{ $installment->due_date ? \Carbon\Carbon::parse($installment->due_date)->format('Y-m-d') : '' }}">
                                            </div>
                                        </div>
                                        <div class="col-xl-2">
                                            <div class="general_form_input d-flex align-items-end"> {{-- Added d-flex and align-items-end --}}
                                                <div class="flex-grow-1 me-2"> {{-- Added flex-grow-1 and me-2 --}}
                                                    <label class="form-label">Amount</label>
                                                    <input type="number" step="0.01" name="installments[{{ $index }}][amount]" value="{{ $installment->amount }}" class="form-control installment-amount" {{ $installment->status == 'paid' ? 'readonly' : '' }}>
                                                </div>
                                                {{-- Lock/Unlock Button --}}
                                                <button type="button" class="btn btn-sm btn-outline-secondary lock-toggle" title="{{ $installment->status == 'paid' ? 'Amount is paid and locked' : 'Lock/Unlock Amount' }}" {{ $installment->status == 'paid' ? 'disabled' : '' }}>
                                                    <i class="fa-solid {{ $installment->status == 'paid' ? 'fa-lock' : 'fa-lock-open' }}"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-xl-2">
                                            <div class="general_form_input">
                                                <label class="form-label">Status</label>
                                                <select name="installments[{{ $index }}][status]" class="form-control form-select">
                                                    <option value="pending" {{ $installment->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="paid" {{ $installment->status == 'paid' ? 'selected' : '' }}>Paid</option>
                                                    <option value="failed" {{ $installment->status == 'failed' ? 'selected' : '' }}>Failed</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xl-2">
                                            <div class="general_form_input">
                                                <label class="form-label">Paid at</label>
                                                <input type="date" name="installments[{{ $index }}][paid_at]" class="form-control" value="{{ $installment->paid_at ? \Carbon\Carbon::parse($installment->paid_at)->format('Y-m-d') : '' }}">
                                            </div>
                                        </div>
                                        <div class="col-xl-2">
                                            <div class="general_form_input d-flex align-items-end"> {{-- Added d-flex and align-items-end --}}
                                                <div class="flex-grow-1 me-2"> {{-- Added flex-grow-1 and me-2 --}}
                                                    <label class="form-label">Note</label>
                                                    <textarea name="installments[{{ $index }}][note]" class="form-control">{{ $installment->note }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-1 d-flex align-items-center">
                                            <div class="general_form_input">
                                                <label class="form-label">&nbsp;</label>
                                                <button type="button" class="btn btn-danger btn-sm remove-installment w-100">Remove</button>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="installments[{{ $index }}][id]" value="{{ $installment->id }}">
                                @endforeach
                            </div>

                            <div class="mt-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold">Commissions</span>
                                    <button type="button" class="btn btn-sm btn-primary" id="addCommission">
                                        <i class="fa fa-plus me-2"></i> Add Commission
                                    </button>
                                </div>
                                <hr class="mt-1 mb-3">
                            </div>

                            <div id="commissionsContainer">
                                @foreach ($commissions as $index => $commission)
                                    <div class="row commission-row mb-2 align-items-center">
                                        <div class="col-xl-1" style="width: 50px">
                                            <div class="general_form_input">
                                                <label class="form-label">&nbsp;</label>
                                                <p># {{ $index+1 }}</p>
                                            </div>
                                        </div>
                                        
                                        <div class="col-xl-1">
                                            <div class="general_form_input">
                                                <label class="form-label">Amount</label>
                                                <input type="number" name="commissions[{{ $index }}][amount]" class="form-control" value="{{ $commission->amount }}">
                                            </div>
                                        </div>

                                        <div class="col-xl-2">
                                            <div class="general_form_input">
                                                <label class="form-label">Commission To <small>(User)</small></label>
                                                <select name="commissions[{{ $index }}][user_id]" class="form-control form-select">
                                                    <option value="">Select a person</option>
                                                    @foreach($commissionUsers as $commissionUser)
                                                        <option value="{{ $commissionUser->id }}" @selected($commissionUser->id == $commission->user_id)>
                                                            {{ $commissionUser->name }} ({{ $commissionUser->mainRole->name }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-xl-2">
                                            <div class="general_form_input">
                                                <label class="form-label">Commission To <small>(External Person)</small></label>
                                                <input type="text" name="commissions[{{ $index }}][payee_name]" class="form-control" value="{{ $commission->payee_name }}" placeholder="External name (if not a user)" {{ $commission->user_id ? 'disabled' : '' }}>
                                            </div>
                                        </div>
                                        
                                        <div class="col-xl-1">
                                            <div class="general_form_input">
                                                <label class="form-label">Status</label>
                                                <select name="commissions[{{ $index }}][status]" class="form-control form-select">
                                                    <option value="paid" {{ $commission->status == 'paid' ? 'selected' : '' }}>Paid</option>
                                                    <option value="unpaid" {{ $commission->status == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xl-2">
                                            <div class="general_form_input">
                                                <label class="form-label">Paid at</label>
                                                <input type="date" name="commissions[{{ $index }}][paid_at]" class="form-control" value="{{ $commission->paid_at ? \Carbon\Carbon::parse($commission->paid_at)->format('Y-m-d') : '' }}">
                                            </div>
                                        </div>
                                        <div class="col-xl-2">
                                            <div class="general_form_input d-flex align-items-end"> {{-- Added d-flex and align-items-end --}}
                                                <div class="flex-grow-1 me-2"> {{-- Added flex-grow-1 and me-2 --}}
                                                    <label class="form-label">Note</label>
                                                    <textarea name="commissions[{{ $index }}][note]" class="form-control">{{ $commission->note }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-1 d-flex align-items-center">
                                            <div class="general_form_input">
                                                <label class="form-label">&nbsp;</label>
                                                <button type="button" class="btn btn-danger btn-sm remove-commission w-100">Remove</button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                @endforeach
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
    const commissionUsers = @json($commissionUsers);
</script>

<script>
if (!window.installmentScriptInitialized) {
    window.installmentScriptInitialized = true;

    let notyf;

    document.addEventListener('DOMContentLoaded', function () {
        
        notyf = new Notyf({
            duration: 5000,
            dismissible: true,
            position: { x: 'right', y: 'top' }
        });

        const amountInput = document.getElementById('amount');
        const discountInput = document.getElementById('discount');
        const totalInput = document.getElementById('total');
        const installmentsContainer = document.getElementById('installmentsContainer');
        const addBtn = document.getElementById('addInstallment');

        // Function to recalculate total based on amount and discount
        function updateTotal() {
            const amount = parseFloat(amountInput.value) || 0;
            const discount = parseFloat(discountInput.value) || 0;
            const total = amount - discount;
            totalInput.value = total.toFixed(2);
            // When amount or discount changes, force a full redistribution among UNLOCKED installments.
            distributeInstallments(); // No args means it's a full redistribution
        }

        // Main function to manage installment amounts based on lock status
        // This function now ALWAYS distributes among UNLOCKED inputs.
        function distributeInstallments() {
            const totalTarget = parseFloat(totalInput.value) || 0;
            const installmentRows = Array.from(document.querySelectorAll('.installment-row'));

            let lockedSum = 0; // Sum of amounts for paid OR manually locked installments
            let unlockedInputs = []; // Inputs that can be changed automatically

            // 1. Calculate locked sum and identify unlocked inputs
            installmentRows.forEach(row => {
                const amountInput = row.querySelector('.installment-amount');
                const isPaid = row.dataset.status === 'paid';
                const isManuallyLocked = row.dataset.locked === 'true' && !isPaid; // True if manually locked AND not paid

                if (isPaid || isManuallyLocked) {
                    lockedSum += parseFloat(amountInput.value) || 0;
                } else {
                    unlockedInputs.push(amountInput);
                }
            });

            const remainingForUnlocked = totalTarget - lockedSum;
            const unlockedCount = unlockedInputs.length;

            if (unlockedCount === 0) {
                // If no unlocked installments, just ensure the sum matches total.
                if (Math.abs(lockedSum - totalTarget) > 0.01) {
                    console.warn("All installments are locked (paid or manually), but their sum doesn't match the total. Manual adjustment is needed.");
                    // Optionally, alert the user or change totalInput color
                }
                return;
            }

            // Distribute remaining value evenly among all unlocked inputs.
            const evenAmount = Math.floor((remainingForUnlocked / unlockedCount) * 100) / 100;
            let remainderCents = Math.round((remainingForUnlocked - evenAmount * unlockedCount) * 100);

            unlockedInputs.forEach((input, index) => {
                let amountToSet = evenAmount;
                if (remainderCents > 0) {
                    amountToSet += 0.01;
                    remainderCents--;
                }
                input.value = amountToSet.toFixed(2);
            });

            // Final check for floating point precision:
            let finalSumCheck = 0;
            installmentRows.forEach(row => {
                finalSumCheck += parseFloat(row.querySelector('.installment-amount').value) || 0;
            });

            if (Math.abs(finalSumCheck - totalTarget) > 0.01) {
                // Apply any remaining small difference to the last unlocked input
                if (unlockedInputs.length > 0) {
                    const lastUnlocked = unlockedInputs[unlockedInputs.length - 1];
                    lastUnlocked.value = (parseFloat(lastUnlocked.value) + (totalTarget - finalSumCheck)).toFixed(2);
                }
            }
        }

        // --- Event Listeners ---

        // Listen for changes in Amount and Discount
        amountInput.addEventListener('input', updateTotal);
        discountInput.addEventListener('input', updateTotal);

        // Listen for manual changes in individual installment amounts (to auto-lock them)
        installmentsContainer.addEventListener('input', function (e) {
            if (e.target.classList.contains('installment-amount') && !e.target.readOnly) {
                const row = e.target.closest('.installment-row');
                // If manually typed, automatically lock this installment
                if (row.dataset.locked !== 'true') { // Avoid re-setting if already locked
                    row.dataset.locked = 'true';
                    const lockIcon = row.querySelector('.lock-toggle i');
                    if (lockIcon) {
                        lockIcon.classList.remove('fa-lock-open');
                        lockIcon.classList.add('fa-lock');
                    }
                }
                // Trigger redistribution among the *remaining* unlocked installments
                distributeInstallments();
            }
        });

        // Listen for clicks on the lock/unlock toggle buttons
        installmentsContainer.addEventListener('click', function (e) {
            if (e.target.classList.contains('lock-toggle') || e.target.closest('.lock-toggle')) {
                const button = e.target.closest('.lock-toggle');
                if (button.disabled) return; // Cannot unlock paid installments

                const row = button.closest('.installment-row');
                const amountInput = row.querySelector('.installment-amount');
                const lockIcon = button.querySelector('i');

                // Toggle the locked state
                if (row.dataset.locked === 'true') {
                    row.dataset.locked = 'false';
                    lockIcon.classList.remove('fa-lock');
                    lockIcon.classList.add('fa-lock-open');
                    // Enable input if it was read-only due to manual lock (not paid)
                    if (row.dataset.status !== 'paid') {
                        amountInput.readOnly = false;
                    }
                } else {
                    row.dataset.locked = 'true';
                    lockIcon.classList.remove('fa-lock-open');
                    lockIcon.classList.add('fa-lock');
                    // Disable input if manually locked
                    amountInput.readOnly = true;
                }
                distributeInstallments(); // Re-distribute after lock/unlock
            }
        });

        // Initialize installment index for new rows
        let installmentIndex = {{ $installments->count() }};
        if (installmentIndex === 0 && installmentsContainer.children.length > 0) {
             installmentIndex = installmentsContainer.children.length;
        }

        // Add Installment button logic
        addBtn.addEventListener('click', function () {
            const lastDateInput = installmentsContainer.querySelector('.installment-row:last-child input[type="date"]');
            let nextDate = new Date(); // Default to current date if no previous installments

            if (lastDateInput && lastDateInput.value) {
                // Parse the date string into components to avoid timezone/daylight saving issues
                const [year, month, day] = lastDateInput.value.split('-').map(Number);

                // Create a new Date object using these components, assuming UTC to avoid local timezone issues
                const lastDate = new Date(Date.UTC(year, month - 1, day)); // month - 1 because Date months are 0-indexed

                // Calculate the target month and year
                let newMonth = lastDate.getUTCMonth() + 1; // getUTCMonth is 0-indexed
                let newYear = lastDate.getUTCFullYear();

                if (newMonth > 11) { // If month becomes December (11), roll over to next year
                    newMonth = 0; // January
                    newYear++;
                }

                const newDay = lastDate.getUTCDate();

                // Create the new date for the next month
                nextDate = new Date(Date.UTC(newYear, newMonth, newDay));

                // Handle cases where the original day doesn't exist in the new month
                // e.g., Jan 31 -> Feb 28/29. If the date rolled over (e.g., to March),
                // it means the day (31st) was not valid for February, so it became Feb 28/29.
                // We want to make sure it stays within the new month's last day if the original day is too high.
                if (nextDate.getUTCMonth() !== newMonth) {
                    // If getUTCMonth() is not the expected newMonth, it means the day rolled over.
                    // Set it to the last day of the *expected* newMonth.
                    nextDate = new Date(Date.UTC(newYear, newMonth + 1, 0)); // Day 0 of next month is last day of current month
                }
            }

            const nextDateStr = nextDate.toISOString().split('T')[0];






            const row = document.createElement('div');
            row.className = 'row installment-row mb-2 align-items-center';
            // New installments start unlocked (data-locked="false")
            row.dataset.status = 'pending'; // New installments are pending
            row.dataset.locked = 'false'; // New installments are unlocked
            row.innerHTML = `
                <div class="col-xl-1" style="width: 50px">
                    <div class="general_form_input">
                        <label class="form-label">&nbsp;</label>
                        <p># ${installmentIndex+1}</p>
                    </div>
                </div>
                <div class="col-xl-2">
                    <div class="general_form_input">
                        <label class="form-label">Due Date</label>
                        <input type="date" name="installments[${installmentIndex}][due_date]" class="form-control" value="${nextDateStr}" required>
                    </div>
                </div>
                <div class="col-xl-2">
                    <div class="general_form_input d-flex align-items-end">
                        <div class="flex-grow-1 me-2">
                            <label class="form-label">Amount</label>
                            <input type="number" step="0.01" name="installments[${installmentIndex}][amount]" class="form-control installment-amount" required>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary lock-toggle" title="Lock/Unlock Amount">
                            <i class="fa-solid fa-lock-open"></i>
                        </button>
                    </div>
                </div>
                <div class="col-xl-2">
                    <div class="general_form_input">
                        <label class="form-label">Status</label>
                        <select name="installments[${installmentIndex}][status]" class="form-control form-select">
                            <option value="pending" selected>Pending</option>
                            <option value="paid">Paid</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>
                </div>
                <div class="col-xl-2">
                    <div class="general_form_input">
                        <label class="form-label">Paid at</label>
                        <input type="date" name="installments[${installmentIndex}][paid_at]" class="form-control">
                    </div>
                </div>
                <div class="col-xl-2">
                    <div class="general_form_input d-flex align-items-end"> {{-- Added d-flex and align-items-end --}}
                        <div class="flex-grow-1 me-2">
                            <label class="form-label">Note</label>
                            <textarea name="installments[${installmentIndex}][note]" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                <div class="col-xl-1 d-flex align-items-center">
                    <div class="general_form_input">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-danger btn-sm remove-installment w-100">Remove</button>
                    </div>
                </div>
            `;
            installmentsContainer.appendChild(row);
            installmentIndex++;
            distributeInstallments(); // Redistribute after adding a new row
        });

        // Remove Installment button logic
        installmentsContainer.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-installment') || e.target.closest('.remove-installment')) {
                const rowToRemove = e.target.closest('.installment-row');

                if (!rowToRemove) return;

                // Check for locked (data-locked="true") or paid status
                const isLocked = rowToRemove.dataset.locked === 'true';
                const status = rowToRemove.querySelector('[name$="[status]"]')?.value;

                if (isLocked || status === 'paid') {
                    notyf.error('You cannot delete paid or locked installments!');
                    return;
                }

                rowToRemove.remove();
                distributeInstallments(); // Redistribute after removing a row
            }
        });

        // Status change listener to toggle readonly, lock status, and redistribute
        installmentsContainer.addEventListener('change', function (e) {
            if (e.target.tagName === 'SELECT' && e.target.name.includes('[status]')) {
                const row = e.target.closest('.installment-row');
                const amountInput = row.querySelector('.installment-amount');
                const lockButton = row.querySelector('.lock-toggle');
                const lockIcon = lockButton ? lockButton.querySelector('i') : null;
                const newStatus = e.target.value;

                // Update data-status
                row.dataset.status = newStatus;

                if (newStatus === 'paid') {
                    amountInput.readOnly = true;
                    row.dataset.locked = 'true';
                    if (lockButton) lockButton.disabled = true;
                    if (lockIcon) {
                        lockIcon.classList.remove('fa-lock-open');
                        lockIcon.classList.add('fa-lock');
                    }

                    // ✅ DO NOT call distributeInstallments()
                    return;
                }

                // For non-paid status (pending, failed)
                amountInput.readOnly = (row.dataset.locked === 'true');
                if (lockButton) lockButton.disabled = false;
                if (lockIcon) {
                    if (row.dataset.locked === 'true') {
                        lockIcon.classList.remove('fa-lock-open');
                        lockIcon.classList.add('fa-lock');
                    } else {
                        lockIcon.classList.remove('fa-lock');
                        lockIcon.classList.add('fa-lock-open');
                    }
                }

                // ✅ Only redistribute when switching *away* from paid status
                distributeInstallments();
            }
        });



        // Initialize Commission
        const addCommissionBtn = document.getElementById('addCommission');
        const commissionsContainer = document.getElementById('commissionsContainer');
        let commissionIndex = {{ $commissions->count() }};

        // Add Commission button logic
        addCommissionBtn.addEventListener('click', function() {
            const row = document.createElement('div');
            row.className = 'row commission-row mb-2 align-items-center';
            let userOptions = '';
                commissionUsers.forEach(user => {
                    const roleName = (user.main_role_relation?.[0]?.name || '').replace(/\b\w/g, c => c.toUpperCase());
                    userOptions += `<option value="${user.id}">${user.name} (${roleName})</option>`;
                });
            row.innerHTML = `
                <div class="col-xl-1" style="width: 50px">
                    <div class="general_form_input">
                        <label class="form-label">&nbsp;</label>
                        <p># ${commissionIndex + 1}</p>
                    </div>
                </div>
                <div class="col-xl-1">
                    <div class="general_form_input">
                        <label class="form-label">Amount</label>
                        <input type="number" step="0.01" name="commissions[${commissionIndex}][amount]" class="form-control" required>
                    </div>
                </div>
                <div class="col-xl-2">
                    <div class="general_form_input">
                        <label class="form-label">Commission To <small>(User)</small></label>
                        <select name="commissions[${commissionIndex}][user_id]" class="form-control form-select">
                            <option value="">Select a person</option>
                            ${userOptions}
                        </select>
                    </div>
                </div>
                <div class="col-xl-2">
                    <div class="general_form_input">
                        <label class="form-label">Commission To <small>(External Person)</small></label>
                        <input type="text" name="commissions[${commissionIndex}][payee_name]" class="form-control" placeholder="External name (if not a user)">
                    </div>
                </div>
                <div class="col-xl-1">
                    <div class="general_form_input">
                        <label class="form-label">Status</label>
                        <select name="commissions[${commissionIndex}][status]" class="form-control form-select">
                            <option value="unpaid" selected>Unpaid</option>
                            <option value="paid">Paid</option>
                        </select>
                    </div>
                </div>
                <div class="col-xl-2">
                    <div class="general_form_input">
                        <label class="form-label">Paid at</label>
                        <input type="date" name="commissions[${commissionIndex}][paid_at]" class="form-control">
                    </div>
                </div>
                <div class="col-xl-2">
                    <div class="general_form_input d-flex align-items-end">
                        <div class="flex-grow-1 me-2">
                            <label class="form-label">Note</label>
                            <textarea name="commissions[${commissionIndex}][note]" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                <div class="col-xl-1 d-flex align-items-center">
                    <div class="general_form_input">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-danger btn-sm remove-commission w-100">Remove</button>
                    </div>
                </div>
            `;
            commissionsContainer.appendChild(row);
            commissionIndex++;
        });

        // Remove Commission button logic
        commissionsContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-commission') || e.target.closest('.remove-commission')) {
                const rowToRemove = e.target.closest('.commission-row');

                // Check for locked (data-locked="true") or paid status
                const status = rowToRemove.querySelector('[name$="[status]"]')?.value;

                if (status === 'paid') {
                    notyf.error('You cannot delete paid commissions!');
                    return;
                }

                rowToRemove.remove();

            }
        });



        // Clears and disables Commission To (External Person) when a user is selected
        const container = document.getElementById('commissionsContainer');

        container.addEventListener('change', function (e) {
            if (e.target.matches('select[name^="commissions"][name$="[user_id]"]')) {
                const row = e.target.closest('.commission-row');
                const externalInput = row.querySelector('input[name^="commissions"][name$="[payee_name]"]');

                if (e.target.value) {
                    externalInput.value = '';
                    externalInput.disabled = true;
                } else {
                    externalInput.disabled = false;
                }
            }
        });

      


    });
}
</script>



@endpush