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
                    <a href="{{ route('admin.setting-recruitment-status.index') }}" class="btn btn-dark px-2 py-1 px-md-3 py-md-2">
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
                    <h3 class="card-title">RECRUITMENT STATUS CREATE</h3>
                </div>
                <div class="card-body">
                <div class="add_course_basic_info">
                    <form action="{{ route('admin.setting-recruitment-status.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-xl-6">
                                <div class="general_form_input">
                                    <label class="label-required">Name</label>
                                    <input type="text" name="name" value="{{ old('name') }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="general_form_input">
                                    <label>Label</label>
                                    <input type="text" name="label" value="{{ old('label') }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="general_form_input">
                                    <label>Color</label>
                                    <input type="text" name="color" value="{{ old('color') }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="general_form_input">
                                    <label class="label-required">Status</label>
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
