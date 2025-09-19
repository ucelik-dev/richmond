@extends('admin.layouts.master')



@section('content')
    <div class="page-body">
        <div class="container-xl">

            <div class="row mb-2">
              <div class="col-xl-12">
                <div class="accordion bg-white" id="accordion-example">
                    
                  <div class="accordion-item">
                    <h2 class="accordion-header" id="heading-1">
                      <button class="accordion-button collapsed text-danger fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-1" aria-expanded="false">
                        Late Installments ({{ $lateInstallmentsCount }}) : {{ currency_format($lateInstallmentsAmount) }}
                      </button>
                    </h2>
                    <div id="collapse-1" class="accordion-collapse collapse" data-bs-parent="#accordion-example" style="">
                      <div class="accordion-body pt-0">
                        
                        @if($lateInstallments->count())
                            
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Student</th>
                                        <th>Phone</th>
                                        <th>Course</th>
                                        <th>Due Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($lateInstallments as $installment)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <a href="{{ route('admin.payment.edit', $installment->payment->id) }}" class="btn-sm btn-primary me-2 text-decoration-none">
                                                    {{ $installment->payment->user->name ?? 'N/A' }}
                                                </a>
                                                
                                            </td>
                                            <td>{{ $installment->payment->user->phone ?? 'N/A' }}</td>
                                            <td class="text-nowrap">
                                                    {{ $installment->payment->course->title ?? 'Unknown Course' }} ({{ $installment->payment->course->level->name ?? '' }})
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($installment->due_date)->format('d-m-Y') }}</td>
                                            <td>{{ currency_format($installment->amount) }}</td>
                                            <td>{{ ucfirst($installment->status) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif

                      </div>
                    </div>
                  </div>
              
                </div>
              </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">PAYMENTS</h3>
                    <div class="card-actions">
                        @if(auth()->user()?->canResource('admin_payments','create'))
                            <a href="{{ route('admin.payment.create') }}" class="btn btn-default">
                                <i class="fa-solid fa-plus me-2"></i>
                                Add new
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