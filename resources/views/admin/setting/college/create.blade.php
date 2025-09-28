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
                    <a href="{{ route('admin.setting-college.index') }}" class="btn btn-dark px-2 py-1 px-md-3 py-md-2">
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
                    <h3 class="card-title">COLLEGE CREATE</h3>
                </div>
                <div class="card-body">
                <div class="add_course_basic_info">
                    <form action="{{ route('admin.setting-college.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="general_form_input">
                                <label for="#">Logo</label><br>
                                <input type="file" name="logo" class="form-control mt-2">
                                <x-input-error :messages="$errors->get('logo')" class="mt-2 text-danger small" />
                            </div>
                            <div class="col-xl-6">
                                <div class="general_form_input">
                                    <label class="label-required">Name</label>
                                    <input type="text" name="name" value="{{ old('name') }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="general_form_input">
                                    <label>Code</label>
                                    <input type="text" name="code" value="{{ old('code') }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="general_form_input">
                                    <label>Url</label>
                                    <input type="text" name="url" value="{{ old('url') }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="general_form_input">
                                    <label>Phone</label>
                                    <input type="text" name="phone" value="{{ old('phone') }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="general_form_input">
                                    <label>Email</label>
                                    <input type="text" name="email" value="{{ old('email') }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="general_form_input">
                                    <label>Status</label>
                                    <select name="status" class="form-control form-select">
                                        <option value="1" selected>Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
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
