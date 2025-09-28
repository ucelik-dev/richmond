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
                    <a href="{{ route('admin.setting-student-batch.index') }}" class="btn btn-dark px-2 py-1 px-md-3 py-md-2">
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
                    <h3 class="card-title">STUDENT BATCH UPDATE</h3>
                </div>
                <div class="card-body">

                    <div class="add_course_basic_info">

                        <form action="{{ route('admin.setting-student-batch.update', $batch->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-xl-3">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Name</label>
                                        <input type="text" name="name" value="{{ $batch->name }}" class="form-control">
                                        <x-input-error :messages="$errors->get('name')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-3">
                                    <div class="general_form_input">
                                        <label for="#">Color</label>
                                        <input type="text" name="color" value="{{ $batch->color }}" class="form-control">
                                        <x-input-error :messages="$errors->get('color')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-3">
                                    <div class="general_form_input">
                                        <label for="#">Start Date</label>
                                        <input type="date" name="start_date" value="{{ $batch->start_date }}" class="form-control">
                                        <x-input-error :messages="$errors->get('start_date')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-3">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Status</label>
                                        <select class="form-control form-select" name="status">
                                            <option @selected($batch->status == 1) value="1">Active</option>
                                            <option @selected($batch->status == 0) value="0">Inactive</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('status')" class="mt-2 text-danger small" />
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