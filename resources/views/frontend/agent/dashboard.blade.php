@extends('frontend.layouts.master')

@section('content')

    <!--===========================
        DASHBOARD OVERVIEW START
    ============================-->
    <section class="wsus__dashboard mt_150 pb_50">
        <div class="container-fluid px-4">
            <div class="row">
                
                @include('frontend.agent.sidebar')

                <div class="col-xl-10 col-md-8 wow fadeInRight" style="visibility: visible; animation-name: fadeInRight;">

                    <div class="wsus__dashboard_content">
                        <div class="wsus__dashboard_content_top d-flex flex-wrap justify-content-between">
                            <div class="wsus__dashboard_heading">
                                <h5>{{ auth()->user()->company ?? auth()->user()->name }}</h5>
                            </div>
                        </div>

                        <div class="wsus__dash_course_table">
                            <div class="row p-4">

                                <div class="col-12 col-xl-6 col-xxl-3 p-2">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="documentsTable">
                                            <thead>
                                                <tr>
                                                    <th class="text-nowrap">Agent Rates</th>
                                                    <th class="text-nowrap">Value</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-nowrap">Agent Commission (£)</td>
                                                    <td>{{ $agent->agentProfile->commission_amount ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-nowrap">Agent Commission (%)</td>
                                                    <td>{{ $agent->agentProfile->commission_percent ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-nowrap">Agent Discount (£)</td>
                                                    <td>{{ $agent->agentProfile->discount_amount ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-nowrap">Agent Discount (%)</td>
                                                    <td>{{ $agent->agentProfile->discount_percent ?? '-' }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                </div>

                                <div class="col-12 col-xl-6 col-xxl-3 p-2">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="documentsTable">
                                            <thead>
                                                <tr>
                                                    <th class="text-nowrap">Registrations</th>
                                                    <th class="text-nowrap">Count</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-nowrap">This Month</td>
                                                    <td>{{ $agentRegistrationThisMonth ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-nowrap">This Year</td>
                                                    <td>{{ $agentRegistrationThisYear ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-nowrap fw-medium">Total</td>
                                                    <td class="fw-medium">{{ $agentRegistrations->count() }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                </div>

                                <div class="col-12 col-xl-6 col-xxl-3 p-2">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="documentsTable">
                                            <thead>
                                                <tr>
                                                    <th class="text-nowrap">Commissions</th>
                                                    <th class="text-nowrap">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-nowrap">This Month</td>
                                                    <td>{{ currency_format($agentCommissionThisMonth) ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-nowrap">This Year</td>
                                                    <td>{{ currency_format($agentCommissionThisYear) ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-nowrap fw-medium">Total</td>
                                                    <td class="fw-medium">{{ currency_format($agentCommissions) }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                </div>

                                <div class="col-12 col-xl-6 col-xxl-3 p-2">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="documentsTable">
                                            <thead>
                                                <tr>
                                                    <th class="text-nowrap">Commissions (Total)</th>
                                                    <th class="text-nowrap">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-nowrap">Paid</td>
                                                    <td>{{ currency_format($agentCommissionsPaid) ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-nowrap">Unpaid</td>
                                                    <td>{{ currency_format($agentCommissionsUnpaid) ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-nowrap fw-medium">Total</td>
                                                    <td class="fw-medium">{{ currency_format($agentCommissions) }}</td>
                                                </tr>
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
    <!--===========================
        DASHBOARD OVERVIEW END
    ============================-->
    
@endsection