@extends('admin.layouts.master')

@section('content')
<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">EXPENSE CREATE</h3>
                <div class="card-actions">
                    <a href="{{ route('admin.expense.index') }}" class="btn btn-dark px-2 py-1 px-md-3 py-md-2">
                        <i class="fa-solid fa-arrow-left me-2"></i>
                        Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="add_course_basic_info">
                    <form action="{{ route('admin.expense.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-xl-4">
                                <div class="general_form_input">
                                    <label class="label-required">Category</label>
                                    <select class="form-control form-select" name="expense_category_id" id="categorySelect">
                                        <option value=""> Please Select </option>
                                        @foreach ($expenseCategories as $expenseCategory)
                                            <option value="{{ $expenseCategory->id }}" data-recurring="{{ $expenseCategory->is_recurring }}">
                                                {{ $expenseCategory->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-2">
                                <div class="general_form_input">
                                    <label class="label-required">Amount</label>
                                    <input type="number" step="0.01" name="amount" value="{{ old('amount') }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-xl-2">
                                <div class="general_form_input">
                                    <label>Transaction Fee</label>
                                    <input type="number" step="0.01" name="transaction_fee" value="{{ old('transaction_fee') }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-xl-2">
                                <div class="general_form_input">
                                    <label class="label-required">Status</label>
                                    <select class="form-control form-select" name="status">
                                        <option @selected(true) value="paid">Paid</option>
                                        <option value="unpaid">Unpaid</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-2">
                                <div class="general_form_input">
                                    <label class="label-required">Date</label>
                                    <input type="date" name="expense_date" class="form-control" value="">
                                </div>
                            </div>
                            <div class="col-xl-12">
                                <div class="general_form_input">
                                    <label>Note</label>
                                    <textarea rows="4" name="note" class="form-control">{{ old('note') }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Recurring Options --}}
                        <div class="row mt-4" id="recurringOptions" style="display: none;">
                            <div class="col-12">
                                <h4>Recurring Setup</h4>
                            </div>
                            <div class="col-xl-2">
                                <div class="general_form_input">
                                    <label>Start Month</label>
                                    <input type="month" name="recurring_start" class="form-control">
                                </div>
                            </div>
                            <div class="col-xl-2">
                                <div class="general_form_input">
                                    <label>End Month</label>
                                    <input type="month" name="recurring_end" class="form-control">
                                </div>
                            </div>
                            <div class="col-xl-2">
                                <div class="general_form_input">
                                    <label>Recurring Amount</label>
                                    <input type="number" step="0.01" name="recurring_amount" class="form-control">
                                </div>
                            </div>
                            <div class="col-xl-2">
                                <div class="general_form_input">
                                    <label>Transaction Fee</label>
                                    <input type="number" step="0.01" name="recurring_transaction_fee" value="{{ old('recurring_transaction_fee') }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-xl-3" id="salaryUserSelect" style="display: none;">
                                <div class="general_form_input">
                                    <label>Select User</label>
                                    <select name="salary_user_id" class="form-control">
                                        <option value="">-- Select --</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-2">
                                <div class="general_form_input">
                                    <label>Status</label>
                                    <select name="recurring_status" class="form-control form-select">
                                        <option value="paid" {{ old('recurring_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="unpaid" {{ old('recurring_status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-12">
                                <div class="general_form_input">
                                    <label>Note</label>
                                    <textarea rows="4" name="recurring_note" class="form-control">{{ old('recurring_note') }}</textarea>
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
    const recurringOptions = document.getElementById('recurringOptions');
    const salarySelect = document.getElementById('salaryUserSelect');

    document.getElementById('categorySelect').addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        const isRecurring = selected.dataset.recurring === "1";

        if (isRecurring) {
            recurringOptions.style.display = 'flex';

            // Show salary select only if category name includes "salary"
            const text = selected.text.toLowerCase();
            if (text.includes('salary')) {
                salarySelect.style.display = 'block';
            } else {
                salarySelect.style.display = 'none';
            }
        } else {
            recurringOptions.style.display = 'none';
            salarySelect.style.display = 'none';
        }
    });
</script>
@endpush
