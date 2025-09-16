@extends('admin.layouts.master')

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">USERS</h3>
                    <div class="card-actions">
                        <a href="{{ route('admin.user.create') }}" class="btn btn-default">
                            <i class="fa-solid fa-plus me-2"></i>
                            Add new
                        </a>
                    </div>
                </div>
                <div class="card-body table-responsive">
                    
                    {{ $dataTable->table() }}

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
@endpush