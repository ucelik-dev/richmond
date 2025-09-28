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
                    <h3 class="card-title">COLLEGE UPDATE</h3>
                </div>
                <div class="card-body">

                    <div class="add_course_basic_info">

                        <form action="{{ route('admin.setting-college.update', $college->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="general_form_input">
                                        <label for="#">Logo</label><br>
                                        <img src="{{ asset($college->logo) }}"
                                            style="width: 150px !important; height: 150px !important; object-fit: contain !important; display: inline-block !important;">
                                        <input type="file" name="logo" class="form-control mt-2">
                                        <x-input-error :messages="$errors->get('logo')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Name</label>
                                        <input type="text" name="name" value="{{ $college->name }}" class="form-control">
                                        <x-input-error :messages="$errors->get('name')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#">Code</label>
                                        <input type="text" name="code" value="{{ $college->code }}" class="form-control">
                                        <x-input-error :messages="$errors->get('code')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#">Url</label>
                                        <input type="text" name="url" value="{{ $college->url }}" class="form-control">
                                        <x-input-error :messages="$errors->get('url')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#">Phone</label>
                                        <input type="text" name="phone" value="{{ $college->phone }}" class="form-control">
                                        <x-input-error :messages="$errors->get('phone')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#">Email</label>
                                        <input type="text" name="email" value="{{ $college->email }}" class="form-control">
                                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-danger small" />
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Status</label>
                                        <select class="form-control form-select" name="status">
                                            <option @selected($college->status == 1) value="1">Active</option>
                                            <option @selected($college->status == 0) value="0">Inactive</option>
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