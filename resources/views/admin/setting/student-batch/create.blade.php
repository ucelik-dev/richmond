@extends('admin.layouts.master')

@section('content')
    <!-- Page header -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Settings
                    </h2>
                </div>
                <div class="col-auto text-end">
                    <a href="{{ route('admin.setting-student-batch.index') }}" class="btn btn-primary">
                        <i class="fa-solid fa-arrow-left me-2"></i>
                        Back
                    </a>
                </div>
            </div>
            <hr class="mt-2 mb-1">
        </div>
    </div>
    <!-- Page body -->
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">STUDENT BATCH CREATE</h3>
                </div>
                <div class="card-body">
                <div class="add_course_basic_info">
                    <form action="{{ route('admin.setting-student-batch.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-xl-3">
                                <div class="general_form_input">
                                    <label class="label-required">Name</label>
                                    <input type="text" name="name" value="{{ old('name') }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-xl-3">
                                <div class="general_form_input">
                                    <label>Color</label>
                                    <input type="text" name="color" value="{{ old('color') }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-xl-3">
                                <div class="general_form_input">
                                    <label for="#">Start Date</label>
                                    <input type="date" name="start_date" value="{{ old('start_date') }}" class="form-control">
                                    <x-input-error :messages="$errors->get('start_date')" class="mt-2 text-danger small" />
                                </div>
                            </div>
                            <div class="col-xl-3">
                                <div class="general_form_input">
                                    <label class="label-required">Status</label>
                                    <select name="status" class="form-control form-select">
                                        <option value="1" selected>Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary mt-4">Create</button>
                    </form>
                </div>
            </div>
            </div>
        </div>
    </div>
@endsection
