@extends('admin.layouts.master')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Settings
                    </h2>
                </div>
                <div class="col-auto text-end">
                    <a href="{{ route('admin.setting.index') }}" class="btn btn-dark px-2 py-1 px-md-3 py-md-2">
                        <i class="fa-solid fa-arrow-left me-2"></i>
                        Back
                    </a>
                </div>
            </div>
            <hr class="mt-2 mb-1">
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            {{-- Loop through each role to create a separate card/table for its permissions --}}
            @forelse ($roles as $role)
                <div class="card mb-4"> {{-- Added mb-4 for vertical spacing between role tables --}}
                    <div class="card-header">
                        <h3 class="card-title">{{ ucfirst($role->name) }} Permissions</h3>
                        <div class="card-actions">
                            <a href="{{ route('admin.setting-user-permission.create') }}" class="btn btn-dark px-2 py-1 px-md-3 py-md-2">
                                <i class="fa-solid fa-plus me-2"></i>
                                Add new
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th style="width:60px;">#</th>
                                    <th>Permission Name</th>
                                    <th>Display Name</th>
                                    <th class="text-center" style="width:95px;">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @forelse ($role->permissions as $permission)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $permission->name }}</td>
                                            <td>{{ $permission->display_name }}</td>
                                            <td class="text-center text-nowrap">
                                                <a href="{{ route('admin.setting-user-permission.edit', $permission->id) }}"
                                                class="btn-sm btn-primary me-2 text-decoration-none" title="Edit">
                                                <i class="fa-solid fa-pen-to-square fa-lg"></i>
                                                </a>
                                                <a href="{{ route('admin.setting-user-permission.destroy', $permission->id) }}"
                                                class="text-red delete-item text-decoration-none" title="Delete">
                                                <i class="fa-solid fa-trash-can fa-lg"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                        <td colspan="4" class="text-muted text-center">No permissions assigned to this role.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            </div>

                    </div>
                </div>
            @empty
                <div class="card">
                    <div class="card-body text-center">
                        <p class="text-muted">No roles found to display permissions.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@endsection