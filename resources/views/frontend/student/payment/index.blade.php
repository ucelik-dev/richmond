@extends('frontend.layouts.master')

@section('content')

    <!-- DASHBOARD OVERVIEW START -->
    <section class="wsus__dashboard mt_150 pb_50">
        <div class="container-fluid px-4">
            <div class="row">

                @include('frontend.student.sidebar')

                <div class="col-xl-10 col-md-8 wow fadeInRight" style="visibility: visible; animation-name: fadeInRight;">

                    <div class="wsus__dashboard_content">
                        <div class="wsus__dashboard_content_top d-flex flex-wrap justify-content-between">
                            <div class="wsus__dashboard_heading">
                                <h5>Payments</h5>
                            </div>
                        </div>

                        <div class="wsus__dash_course_table">
                            <div class="row">
                                <div class="col-12 p-4">

                                    <div class="table-responsive p-1 mb-5 border rounded">

                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>COURSES</th>
                                                    <th>TOTAL</th>
                                                    <th>PAID</th>
                                                    <th>REMAINING</th>
                                                    <th>INSTALLMENTS</th>
                                                    <th>STATUS</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                    
                                                @forelse ($payments as $payment)
                                                    @php
                                                        $paidAmount = $payment->installments->where('status', 'paid')->sum('amount');
                                                        $remainingAmount = $payment->installments->where('status', 'pending')->sum('amount');
                                                    @endphp

                                                    <tr>

                                                        <td class="p-2 text-nowrap">
                                                            <p class="mb-1">{{ $payment->course->title ?? 'Unknown Course' }} ({{ $payment->course->level->name ?? '' }})</p>
                                                        </td>

                                                        <td class="p-2 text-nowrap"><p class="mb-1">{{ currency_format($payment->total) }}</p></td>
                                                        <td class="p-2 text-nowrap"><p class="mb-1">{{ currency_format($paidAmount) }}</p></td>
                                                        <td class="p-2 text-nowrap"><p class="mb-1">{{ currency_format($remainingAmount) }}</p></td>

                                                        <td class="align-top text-nowrap p-3">

                                                            <table class="table table-bordered mb-0">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="p-2">Status</th>
                                                                        <th class="p-2">Amount</th>
                                                                        <th class="p-2">Due Date</th>
                                                                        <th class="p-2">Paid at</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>

                                                                    @forelse ($payment->installments as $installment)
                                                                    
                                                                        @php
                                                                            $status = $installment->status;
                                                                            $colorMap = [
                                                                                'pending' => 'yellow',
                                                                                'paid' => 'green',
                                                                                'failed' => 'red',
                                                                            ];
                                                                            $color = $colorMap[$status] ?? 'secondary';
                                                                        @endphp


                                                                        <tr>
                                                                            <td>
                                                                                <span class="badge bg-{{ $color }} text-{{ $color }}-fg align-self-start mb-1" style="width:70px">
                                                                                    {{ ucwords($status) }}
                                                                                </span>
                                                                            </td>
                                                                            <td>
                                                                                <span class="text-secondary">
                                                                                    {{ currency_format($installment->amount) }}
                                                                                </span>
                                                                            </td>
                                                                            <td>
                                                                                <span class="text-secondary">
                                                                                    {{ \Carbon\Carbon::parse($installment->due_date)->format('d-m-Y') }}
                                                                                </span>
                                                                            </td>
                                                                            <td>
                                                                                @if($installment->paid_at)
                                                                                    <span class="text-secondary">
                                                                                        {{ \Carbon\Carbon::parse($installment->paid_at)->format('d-m-Y') }}
                                                                                    </span>
                                                                                @else
                                                                                    <a href="https://pay.richmondcollege.co.uk/{{ $installment->amount }}" target="_blank">
                                                                                        <span class="badge bg-primary text-primary-fg px-4 py-2">Pay</span>
                                                                                    </a>
                                                                                @endif
                                                                                
                                                                            </td>
                                                                        </tr>
                                                                            
                                                                    @empty
                                                                        <span class="text-muted">No installments</span>
                                                                    @endforelse

                                                                </tbody>
                                                            </table>

                                                        </td>

                                                        <td class="text-secondary text-nowrap">
                                                            <span class="badge bg-{{ $payment->paymentStatus->color }} text-{{ $payment->paymentStatus->color }}-fg">{{ ucwords($payment->paymentStatus->name) }}</span>
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

                    

                 
                </div>

            </div>
        </div>
    </section>
    <!-- DASHBOARD OVERVIEW END -->
@endsection
