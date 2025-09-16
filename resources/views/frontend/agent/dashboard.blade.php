@extends('frontend.layouts.master')

@section('content')

    <!--===========================
        DASHBOARD OVERVIEW START
    ============================-->
    <section class="wsus__dashboard mt_150 pb_50">
        <div class="container-fluid px-4">
            <div class="row">
                
                @include('frontend.agent.sidebar')

                <div class="col-xl-10 col-md-8 pt-0 mt-0">

                    @if(auth()->user()->approve_status === 'pending')
                        <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
                            <symbol id="check-circle-fill" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                            </symbol>
                            <symbol id="info-fill" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                            </symbol>
                            <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                            </symbol>
                        </svg>
                        <div class="alert alert-primary d-flex align-items-center" role="alert">
                            <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Info:"><use xlink:href="#info-fill"/></svg>
                            <div>
                                Hi {{ auth()->user()->name }}! Your application is currently {{ auth()->user()->approve_status }}. We will send you an email when it is approved.
                            </div>
                        </div>
                    @endif

                    <p class="fw-medium fs-6">{{ $agent->name }}</p>
                    <hr class="mt-1">

                    <div class="row mb-4">
                        <div class="col-xl-2 col-sm-6 wow fadeInUp">
                            <div class="wsus__dash_earning">
                                <h6>Agent Code</h6>
                                <h3>{{$agent->agentProfile->agent_code ?? '-' }}</h3>
                            </div>
                        </div>
                        <div class="col-xl-2 col-sm-6 wow fadeInUp">
                            <div class="wsus__dash_earning">
                                <h6>Agent Commission (£)</h6>
                                <h3>{{ $agent->agentProfile->commission_amount ?? '-' }}</h3>
                            </div>
                        </div>
                        <div class="col-xl-2 col-sm-6 wow fadeInUp">
                            <div class="wsus__dash_earning">
                                <h6>Agent Commission (%)</h6>
                                <h3>{{ $agent->agentProfile->commission_percent ?? '-' }}</h3>
                            </div>
                        </div>
                        <div class="col-xl-2 col-sm-6 wow fadeInUp">
                            <div class="wsus__dash_earning">
                                <h6>Agent Discount (£)</h6>
                                <h3>{{ $agent->agentProfile->discount_amount ?? '-' }}</h3>
                            </div>
                        </div>
                        <div class="col-xl-2 col-sm-6 wow fadeInUp">
                            <div class="wsus__dash_earning">
                                <h6>Agent Discount (%)</h6>
                                <h3>{{ $agent->agentProfile->discount_percent ?? '-' }}</h3>
                            </div>
                        </div>
                        
                    </div>

                    <p class="fw-medium fs-6">Registrations</p>
                    <hr class="mt-1">

                    <div class="row mb-4">
                        <div class="col-xl-2 col-sm-6 wow fadeInUp">
                            <div class="wsus__dash_earning">
                                <h6>Total</h6>
                                <h3>{{ $agentRegistrations->count() }}</h3>
                            </div>
                        </div>
                        <div class="col-xl-2 col-sm-6 wow fadeInUp">
                            <div class="wsus__dash_earning">
                                <h6>This Month</h6>
                                <h3>{{ $agentRegistrationThisMonth }}</h3>
                            </div>
                        </div>
                        <div class="col-xl-2 col-sm-6 wow fadeInUp">
                            <div class="wsus__dash_earning">
                                <h6>This Year</h6>
                                <h3>{{ $agentRegistrationThisYear }}</h3>
                            </div>
                        </div>
                        
                    </div>

                    <p class="fw-medium fs-6">Commissions</p>
                    <hr class="mt-1">

                    <div class="row">
                        <div class="col-xl-2 col-sm-6 wow fadeInUp">
                            <div class="wsus__dash_earning">
                                <h6>Total</h6>
                                <h3>{{ currency_format($agentCommissions) }}</h3>
                            </div>
                        </div>
                        <div class="col-xl-2 col-sm-6 wow fadeInUp">
                            <div class="wsus__dash_earning">
                                <h6>Paid</h6>
                                <h3 class="text-success">{{ currency_format($agentCommissionsPaid) }}</h3>
                            </div>
                        </div>
                        <div class="col-xl-2 col-sm-6 wow fadeInUp">
                            <div class="wsus__dash_earning">
                                <h6>Unpaid</h6>
                                <h3 class="text-danger">{{ currency_format($agentCommissionsUnpaid) }}</h3>
                            </div>
                        </div>
                        <div class="col-xl-2 col-sm-6 wow fadeInUp">
                            <div class="wsus__dash_earning">
                                <h6>This Month</h6>
                                <h3>{{ currency_format($agentCommissionThisMonth) }}</h3>
                            </div>
                        </div>
                        <div class="col-xl-2 col-sm-6 wow fadeInUp">
                            <div class="wsus__dash_earning">
                                <h6>This Year</h6>
                                <h3>{{ currency_format($agentCommissionThisYear) }}</h3>
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