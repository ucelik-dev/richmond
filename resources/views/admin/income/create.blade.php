@extends('admin.layouts.master')

@section('content')
<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">INCOME CREATE</h3>
                <div class="card-actions">
                    <a href="{{ route('admin.income.index') }}" class="btn btn-default">
                        <i class="fa-solid fa-arrow-left me-2"></i>
                        Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="add_course_basic_info">
                    <form action="{{ route('admin.income.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-xl-3">
                                <div class="general_form_input">
                                    <label class="label-required">Category</label>
                                    <select class="form-control form-select" name="income_category_id">
                                        <option value=""> Please Select </option>
                                        @foreach ($incomeCategories as $incomeCategory)
                                            <option value="{{ $incomeCategory->id }}">
                                                {{ $incomeCategory->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-3">
                                <div class="general_form_input">
                                    <label class="label-required">Amount</label>
                                    <input type="number" name="amount" value="{{ old('amount') }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-xl-3">
                                <div class="general_form_input">
                                    <label class="label-required">Date</label>
                                    <input type="date" name="income_date" class="form-control" value="{{ old('income_date') }}">
                                </div>
                            </div>
                            <div class="col-xl-3">
                                <div class="general_form_input">
                                    <label class="label-required">Status</label>
                                    <select name="status" class="form-control form-select">
                                        <option value="paid" {{ old('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="unpaid" {{ old('status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-12">
                                <div class="general_form_input">
                                    <label>Note</label>
                                    <textarea rows="4" name="note" class="form-control">{{ old('note') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-default mt-2">Create</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
