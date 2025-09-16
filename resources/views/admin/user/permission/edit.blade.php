@extends('admin.layouts.master')

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">MANAGE PERMISSIONS ({{ $user->name }})</h3>
                    <div class="card-actions">
                        <a href="{{ route('admin.user.index') }}" class="btn btn-primary">
                            <i class="fa-solid fa-arrow-left me-2"></i>
                            Back
                        </a>
                    </div>
                </div>
                <div class="card-body">

                    <div class="add_course_basic_info">

                        <form action="{{ route('admin.user.permission.update', $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            @foreach ($roles as $role)
                                <div class="card shadow mb-4">
                                    <div class="card-header py-3">
                                        <h3 class="m-0 font-weight-bold text-primary">{{ ucwords($role->name) }} Role</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Permission Name</th>
                                                        <th>Display Name</th>
                                                        <th class="text-center">Default</th>
                                                        <th class="text-center">Permission</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($role->permissions->sortBy('display_name') as $permission)
                                                        <tr>
                                                            <td>{{ $permission->name }}</td>
                                                            <td>{{ $permission->display_name }}</td>
                                                            <td class="text-center">
                                                                <input type="checkbox" checked disabled>
                                                            </td>
                                                            <td class="text-center">
                                                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                                                    {{ in_array($permission->id, $userPermissions) ? 'checked' : '' }}>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <div class="d-flex justify-content-end mt-3">
                                <div class="add_course_basic_info_input">
                                    <div class="add_course_basic_info_input d-flex gap-2">
                                        <button type="submit" name="action" value="save_exit" class="btn btn-primary mt_20">Update</button>
                                        <button type="submit" name="action" value="save_stay" class="btn btn-secondary mt_20">Update & Stay</button>
                                    </div>
                                </div>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

