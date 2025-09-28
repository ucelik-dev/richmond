@extends('admin.layouts.master')

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">INCOME UPDATE</h3>
                    <div class="card-actions">
                        <a href="{{ route('admin.income.index') }}" class="btn btn-dark px-2 py-1 px-md-3 py-md-2">
                            <i class="fa-solid fa-arrow-left me-2"></i>
                            Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="add_course_basic_info">
                        <form action="{{ route('admin.income.update', $income->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                
                                <div class="col-xl-3">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Category</label>
                                        <select class="form-control form-select" name="income_category_id">
                                            <option value=""> Please Select </option>
                                            @foreach ($incomeCategories as $incomeCategory)
                                                <option @selected($income->income_category_id === $incomeCategory->id) value="{{ $incomeCategory->id }}"> {{ $incomeCategory->name }} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-3">
                                    <div class="general_form_input">
                                        <label>Amount</label>
                                        <input type="number" name="amount" class="form-control" value="{{ $income->amount }}">
                                    </div>
                                </div>
                                <div class="col-xl-3">
                                    <div class="general_form_input">
                                        <label for="#">Status</label>
                                        <select class="form-control form-select" name="status">
                                            <option @selected($income->status === 'paid') value="paid">Paid</option>
                                            <option @selected($income->status === 'unpaid') value="unpaid">Unpaid</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-3">
                                    <div class="general_form_input">
                                        <label>Date</label>
                                        <input type="date" name="income_date" class="form-control" value="{{ $income->income_date ? \Carbon\Carbon::parse($income->income_date)->format('Y-m-d') : '' }}">
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <div class="general_form_input d-flex align-items-end">
                                        <div class="flex-grow-1">
                                            <label>Note</label>
                                            <textarea rows="4" name="note" class="form-control">{{ $income->note }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-dark px-2 py-1 px-md-3 py-md-2 mt-2">Update</button>
                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection