@extends('admin.layouts.master')

@section('content')
    <div class="page-body">
        <div class="container-xl">

              <div class="card">
                <div class="card-header">
                    <h3 class="card-title">EMAIL LOGS</h3>
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