@extends('frontend.layouts.master')

@section('content')

    <!--===========================
        DASHBOARD OVERVIEW START
    ============================-->

    <section class="wsus__dashboard mt_150 pb_50">
        <div class="container-fluid px-4">
            <div class="row">
                
                @include('frontend.agent.sidebar')

                <div class="col-xl-10 col-md-8">
                    
                    <div class="wsus__dashboard_content">
                        <div class="wsus__dashboard_content_top">
                            <div class="wsus__dashboard_heading relative">
                                <h5>Registrations ({{ $students->count() }})</h5>
                            </div>
                        </div>


                        <div class="wsus__dash_course_table">
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-responsive p-4">

                                        @php
                                            $allCommissions = $students->flatMap(fn($student) =>
                                                $student->payments->flatMap->commissions
                                            );

                                            $totalPaid = $allCommissions->where('status', 'paid')->sum('amount');
                                            $totalUnpaid = $allCommissions->where('status', 'unpaid')->sum('amount');
                                        @endphp

                                        <table class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>NAME</th>
                                                    <th>EMAIL</th>
                                                    <th>PHONE</th>
                                                    <th>COUNTRY</th>
                                                    <th>COURSE</th>
                                                    <th>COMMISSION</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                               
                                                @foreach($students as $student)
                                                    <tr>
                                                        <td class="text-nowrap p-2"><p>{{ $loop->iteration }}</p></td>
                                                        
                                                        <td class="text-nowrap p-2">
                                                            <p class="fw-medium mb-2">{{ $student->name }}</p>
                                                        </td>

                                                        <td class="text-nowrap p-2">
                                                            <p>{{ $student->email }}</p>
                                                        </td>

                                                        <td class="text-nowrap p-2">
                                                            <p>{{ $student->phone }}</p>
                                                        </td>

                                                        <td class="text-nowrap p-2">
                                                            <p>{{ $student->country?->name }}</p>
                                                        </td>

                                                        <td class="text-nowrap p-2">
                                                            @foreach($student->enrollments as $enrollment)
                                                                @if($enrollment->course)
                                                                    <span class="badge bg-secondary text-secondary-fg">
                                                                        {{ $enrollment->course->title }} ({{ $enrollment->course->level->name }})
                                                                    </span><br>
                                                                @endif
                                                            @endforeach
                                                        </td>

                                                        <td class="text-nowrap p-2">
                                                            @php
                                                                $commissions = $student->payments->flatMap->commissions;
                                                            @endphp

                                                            @if ($commissions->isEmpty())
                                                                <span class="text-warning">No commissions</span>
                                                            @else

                                                                    @foreach ($student->payments as $payment)
                                                                            
                                                                        @foreach ($payment->commissions as $commission)

                                                                            <span class="text-secondary">{{ currency_format($commission->amount) }}</span>

                                                                            <span class="badge bg-{{ $commission->status === 'paid' ? 'green' : 'red' }} text-{{ $commission->status === 'paid' ? 'green' : 'red' }}-fg">
                                                                                {{ ucfirst($commission->status) }}
                                                                            </span><br>

                                                                            @if ($commission->paid_at)
                                                                                <small class="text-muted">{{ \Carbon\Carbon::parse($commission->paid_at)->format('d-m-Y') }}</small><br>
                                                                            @endif

                                                                            @if($payment->commissions->count() > 1)
                                                                                <hr class="my-1">
                                                                            @endif

                                                                        @endforeach

                                                                    @endforeach
                                                            
                                                            @endif

                                                        </td>

                                                    </tr>
                                                @endforeach

                                            </tbody>
                                        </table>
                                        
                                    </div>

                                    <div class="mt-2 mb-4 px-4">
                                        <div class="d-flex flex-column align-items-end">
                                            <div class="mb-2">
                                                <strong>Total Paid :</strong>
                                                <span class="text-success fw-bold fs-5">{{ currency_format($totalPaid) }}</span>
                                            </div>
                                            <div>
                                                <strong>Total Unpaid :</strong>
                                                <span class="text-danger fw-bold fs-5">{{ currency_format($totalUnpaid) }}</span>
                                            </div>
                                        </div>
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