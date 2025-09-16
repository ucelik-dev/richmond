@extends('admin.layouts.master')

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">EXPENSE UPDATE</h3>
                    <div class="card-actions">
                        <a href="{{ route('admin.expense.index') }}" class="btn btn-default">
                            <i class="fa-solid fa-arrow-left me-2"></i>
                            Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="add_course_basic_info">
                        <form action="{{ route('admin.expense.update', $expense->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                
                                <div class="col-xl-4">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Category</label>
                                        <select class="form-control form-select" name="expense_category_id">
                                            <option value=""> Please Select </option>
                                            @foreach ($expenseCategories as $expenseCategory)
                                                <option @selected($expense->expense_category_id === $expenseCategory->id) value="{{ $expenseCategory->id }}"> {{ $expenseCategory->name }} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-2">
                                    <div class="general_form_input">
                                        <label class="label-required">Amount</label>
                                        <input type="number" step="0.01" name="amount" class="form-control" value="{{ $expense->amount }}">
                                    </div>
                                </div>
                                <div class="col-xl-2">
                                    <div class="general_form_input">
                                        <label>Transaction Fee</label>
                                        <input type="number" step="0.01" name="transaction_fee" value="{{ $expense->transaction_fee }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-xl-2">
                                    <div class="general_form_input">
                                        <label class="label-required">Status</label>
                                        <select class="form-control form-select" name="status">
                                            <option @selected($expense->status === 'paid') value="paid">Paid</option>
                                            <option @selected($expense->status === 'unpaid') value="unpaid">Unpaid</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-2">
                                    <div class="general_form_input">
                                        <label class="label-required">Date</label>
                                        <input type="date" name="expense_date" class="form-control" value="{{ $expense->expense_date ? \Carbon\Carbon::parse($expense->expense_date)->format('Y-m-d') : '' }}">
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <div class="general_form_input d-flex align-items-end">
                                        <div class="flex-grow-1">
                                            <label>Note</label>
                                            <textarea rows="4" name="note" class="form-control">{{ $expense->note }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-default mt-2">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection