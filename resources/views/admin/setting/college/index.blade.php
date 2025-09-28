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
                    <a href="{{ route('admin.setting.index') }}" class="btn btn-dark px-2 py-1 px-md-3 py-md-2">
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
                    <h3 class="card-title">COLLEGES</h3>
                    <div class="card-actions">
                        <a href="{{ route('admin.setting-college.create') }}" class="btn btn-dark px-2 py-1 px-md-3 py-md-2">
                            <i class="fa-solid fa-plus me-2"></i>
                            Add new
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">#</th>
                                    <th>LOGO</th>
                                    <th>NAME</th>
                                    <th>CODE</th>
                                    <th>URL</th>
                                    <th>PHONE</th>
                                    <th>EMAIL</th>
                                    <th>STATUS</th>
                                    <th style="width: 75px;">ACTION</th>
                                </tr>
                            </thead>
                            <tbody>

                                @forelse ($colleges as $college)
                                    <tr>
                                        <td>{{ $college->id }}</td>
                                        
                                        <td>
                                            <div class="table-avatar">
                                                <img src="{{ asset($college->logo) }}" alt="logo" loading="lazy">
                                            </div>
                                        </td>

                                        <td class="text-secondary text-nowrap">{{ $college->name }}</td>
                                        <td class="text-secondary text-nowrap">{{ $college->code }}</td>
                                        <td class="text-secondary text-nowrap">
                                            <a href="{{ $college->url }}" target="_blank">{{ $college->url }}</a>
                                        </td>
                                        <td class="text-secondary text-nowrap">{{ $college->phone }}</td>
                                        <td class="text-secondary text-nowrap">{{ $college->email }}</td>

                                        <td class="text-secondary text-nowrap">
                                            <span>
                                                @if($college->status === 1)
                                                    <span class="badge bg-green text-green-fg">Active</span>
                                                @elseif($college->status === 0)
                                                    <span class="badge bg-red text-red-fg">Inactive</span>
                                                @endif
                                            </span>
                                        </td>

                                        <td class="text-nowrap">
                                            <a href="{{ route('admin.setting-college.edit', $college->id) }}" class="btn-sm btn-primary me-2 text-decoration-none">
                                                <i class="fa-solid fa-pen-to-square fa-lg"></i>
                                            </a>
                                            <a href="{{ route('admin.setting-college.destroy', $college->id) }}" class="text-red delete-item text-decoration-none">
                                                <i class="fa-solid fa-trash-can fa-lg"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8">No data available.</td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
