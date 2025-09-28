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
                    <h3 class="card-title">DOCUMENT CATEGORIES</h3>
                    <div class="card-actions">
                        <a href="{{ route('admin.setting-document-category.create') }}" class="btn btn-dark px-2 py-1 px-md-3 py-md-2">
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
                                    <th>NAME</th>
                                    <th>ROLE</th>
                                    <th>STATUS</th>
                                    <th style="width: 75px;">ACTION</th>
                                </tr>
                            </thead>
                            <tbody>

                                @forelse ($documentCategories as $documentCategory)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        
                                        <td class="text-secondary text-nowrap">{{ $documentCategory->name }}</td>

                                        <td class="text-secondary text-nowrap">
                                            <span class="badge bg-{{ $documentCategory->role->color }} text-{{ $documentCategory->role->color }}-fg">{{ ucwords($documentCategory->role->name) }}</span>
                                        </td>

                                        <td class="text-secondary text-nowrap">
                                            <span>
                                                @if($documentCategory->status === 1)
                                                    <span class="badge bg-green text-green-fg">Active</span>
                                                @elseif($documentCategory->status === 0)
                                                    <span class="badge bg-red text-red-fg">Inactive</span>
                                                @endif
                                            </span>
                                        </td>

                                        <td class="text-nowrap">
                                            <a href="{{ route('admin.setting-document-category.edit', $documentCategory->id) }}" class="btn-sm btn-primary me-2 text-decoration-none">
                                                <i class="fa-solid fa-pen-to-square fa-lg"></i>
                                            </a>
                                            <a href="{{ route('admin.setting-document-category.destroy', $documentCategory->id) }}" class="text-red delete-item text-decoration-none">
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
