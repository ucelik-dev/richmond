@extends('admin.layouts.master')

@section('content')
<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">AGENTS</h3>
                <div class="card-actions">
                    @if(auth()->user()?->canResource('admin_agents','create'))
                        <a href="{{ route('admin.agent.create') }}" class="btn btn-dark px-2 py-1 px-md-3 py-md-2">
                            <i class="fa-solid fa-plus me-2"></i> Add new
                        </a>
                    @endif
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