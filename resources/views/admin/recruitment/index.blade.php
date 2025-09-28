@extends('admin.layouts.master')

@section('content')
    <div class="page-body">
        <div class="container-xl">


            <div class="row mb-2">
              <div class="col-xl-12">
                <div class="accordion bg-white" id="accordion-example">
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading-1">
                            <button class="accordion-button collapsed text-primary fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-1" aria-expanded="false">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <span class="fs-3">COMMISSIONS ({{ $commissions->count() }})</span>
                                    <div class="d-flex text-right gap-3 me-3">
                                        <span class="badge bg-success text-success-fg">Paid : {{ currency_format($paidAmount) }}</span>
                                        <span class="badge bg-danger text-danger-fg">Unpaid : {{ currency_format($unpaidAmount) }}</span>
                                    </div>
                                </div>
                            </button>
                        </h2>
                 
                        <div id="collapse-1" class="accordion-collapse collapse" data-bs-parent="#accordion-example" style="">
                            <div class="accordion-body pt-0">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Student Name</th>
                                                <th>Student Registration Date</th>
                                                <th>Sales Person Name</th>
                                                <th>Agent Name</th>
                                                <th>Commission Amount</th>
                                                <th>Paid Status</th>
                                                <th>Commission Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($commissions as $commission)
                                            <tr>
                                                <td class="text-nowrap">{{ $loop->iteration }}</td>

                                                {{-- Student Name: from payment->user --}}
                                                <td class="text-nowrap">{{ $commission->payment?->user?->name ?? '-' }}</td>

                                                <td class="text-nowrap">{{ \Carbon\Carbon::parse($commission->payment?->user?->created_at)->format('d-m-Y') }}</td>

                                                {{-- Sales Name: from payment->salesPerson --}}
                                                <td class="text-nowrap">{{ $commission->payment?->user->salesPerson?->name ?? '-' }}</td>

                                                {{-- Agent Name: from payment->agent --}}
                                                <td class="text-nowrap">{{ $commission->payment?->user->agent?->company ?? '-' }}</td>

                                                <td class="text-nowrap">{{ currency_format($commission->amount) }}</td>

                                                <td class="text-nowrap">
                              
                                                    @if($commission->status === 'paid')
                                                        <span class="badge bg-success text-success-fg">{{ $commission->status }}</span>
                                                    @else
                                                        <span class="badge bg-danger text-danger-fg">{{ $commission->status }}</span>
                                                    @endif
                                                
                                                </td>

                                                <td class="text-nowrap">{{ \Carbon\Carbon::parse($commission->created_at)->format('d-m-Y') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
              
                </div>
              </div>
            </div>


            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">RECRUITMENTS</h3>
                    <div class="card-actions">
                        @can('create_admin_recruitments')
                            <a href="{{ route('admin.recruitment.create') }}" class="btn btn-dark px-2 py-1 px-md-3 py-md-2">
                                <i class="fa-solid fa-plus me-2"></i>
                                Add new
                            </a>
                        @endcan
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