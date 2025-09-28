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
                    <a href="{{ route('admin.setting-user-role.index') }}" class="btn btn-dark px-2 py-1 px-md-3 py-md-2">
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
                    <h3 class="card-title">USER PERMISSION CREATE</h3>
                </div>
                <div class="card-body">
                <div class="add_course_basic_info">
                    <form action="{{ route('admin.setting-user-permission.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                         <div class="row">
                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Name</label>
                                        <input type="text" name="name" value="{{ old('name') }}" class="form-control">
                                        <x-input-error :messages="$errors->get('name')" class="mt-2 text-danger small" />
                                    </div>
                                </div>

                                <div class="col-xl-6">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Display Name</label>
                                        <input type="text" name="display_name" value="{{ old('display_name') }}" class="form-control">
                                        <x-input-error :messages="$errors->get('display_name')" class="mt-2 text-danger small" />
                                    </div>
                                </div>

                                <div class="col-xl-12">
                                    <div class="general_form_input">
                                        <label>Description</label>
                                        <input type="text" name="description" value="{{ old('description') }}" class="form-control">
                                        <x-input-error :messages="$errors->get('description')" class="mt-2 text-danger small" />
                                    </div>
                                </div>

                                <div class="col-xl-12">
                                    <div class="general_form_input">
                                        <label for="#" class="label-required">Assign to Roles</label>
                                        <select class="form-control select2-multiple" name="roles[]" multiple>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }}" {{ in_array($role->id, old('roles', [])) ? 'selected' : '' }}>
                                                    {{ ucwords($role->name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('roles')" class="mt-2 text-danger small" />
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
