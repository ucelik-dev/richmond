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
                    <a href="{{ route('admin.setting-income-category.index') }}" class="btn btn-primary">
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
                    <h3 class="card-title">INCOME CATEGORY CREATE</h3>
                </div>
                <div class="card-body">
                <div class="add_course_basic_info">
                    <form action="{{ route('admin.setting-income-category.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-xl-12">
                                <div class="general_form_input">
                                    <label class="label-required">Name</label>
                                    <input type="text" name="name" value="{{ old('name') }}" class="form-control">
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
