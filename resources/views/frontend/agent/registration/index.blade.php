@extends('frontend.layouts.master')

@section('content')

    <!--===========================
        DASHBOARD OVERVIEW START
    ============================-->

    <section class="wsus__dashboard mt_150 pb_50">
        <div class="container-fluid px-4">
            <div class="row">
                
                @include('frontend.agent.sidebar')

                @php
                    $allCommissions = $students->flatMap(fn($student) =>
                        $student->payments->flatMap->commissions
                    );

                    $totalPaid = $allCommissions->where('status', 'paid')->sum('amount');
                    $totalUnpaid = $allCommissions->where('status', 'unpaid')->sum('amount');
                @endphp

                <div class="col-xl-10 col-md-8">
                    
                    <div class="wsus__dashboard_content">
                        <div class="wsus__dashboard_content_top">
                            <div class="d-flex flex-column flex-lg-row align-items-start align-items-center justify-content-between gap-4 gap-md-4">
                                <div class="wsus__dashboard_heading m-0">
                                    <h5 class="m-0">Registrations <span>({{ $students->count() }})</span></h5>
                                </div>
                                <div class="d-flex flex-column flex-md-row align-items-center gap-0 gap-md-4">
                                    <div class="text-nowrap">
                                        <strong>Total Paid :</strong>
                                        <span class="text-success fw-bold">{{ currency_format($totalPaid) }}</span>
                                    </div>
                                    <div class="text-nowrap">
                                        <strong>Total Unpaid :</strong>
                                        <span class="text-danger fw-bold">{{ currency_format($totalUnpaid) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="wsus__dash_course_table">
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-responsive p-4">

                                        {{ $dataTable->table() }}

                                    </div>

                                </div>
                            </div>
                        </div>
                        
                    </div>
               
                    
                </div>
            </div>
        </div>
    </section>
    <!--===========================
        DASHBOARD OVERVIEW END
    ============================-->
    
@endsection

@push('scripts')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
    <style>
        
    </style>
@endpush