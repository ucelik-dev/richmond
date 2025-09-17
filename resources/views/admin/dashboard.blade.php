@extends('admin.layouts.master')

@section('content')
<!-- Page header -->
<div class="page-header d-print-none">
  <div class="container-xl">
    <div class="row g-2 align-items-center">
      <!-- Title: full width on mobile, flex on md+ -->
      <div class="col-12 col-md">
            <div class="d-flex flex-column align-items-center align-items-md-start">
                <div class="page-pretitle mb-1">Richmond College</div>
                <h2 class="page-title mb-2 mb-md-0">Dashboard</h2>
            </div>
        </div>


      <!-- Filters: full width on mobile, right-aligned on md+ -->
      <div class="col-12 col-md-auto ms-md-auto mt-0 mt-md-0 d-print-none">
        <form method="GET" action="{{ route('admin.dashboard') }}">
          <div class="row g-2 align-items-center">
            <div class="col-12 col-md-auto">
              <input type="date" name="start_date" class="form-control filter-input"
                     value="{{ request('start_date') }}">
            </div>
            <div class="col-12 col-md-auto">
              <input type="date" name="end_date" class="form-control filter-input"
                     value="{{ request('end_date') }}">
            </div>
            <div class="col-12 col-md-auto">
              <button class="btn btn-default w-100">Apply</button>
            </div>
            <div class="col-12 col-md-auto">
              <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary w-100">Clear</a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <div class="row row-deck row-cards">

            <hr class="mt-1 mb-2">

            <div class="col-12">
                <div class="row row-cards">

                    {{-- Student Statuses --}}
                    <div class="col-sm-6 col-md-6 col-lg-4 col-xl-3 col-xxl-2">
                        <div class="card card-sm">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <div class="font-weight-medium d-flex mb-2">
                                            <div class="fw-bold">Students Statuses</div>
                                        </div>
                                        
                                        @foreach ($studentStatusCounts as $student)
                                            <div class="d-flex">
                                                <div class="text-secondary">{{ ucwords($student->userStatus->name) }}</div>
                                                <div class="text-secondary ms-auto">{{ $student->count }}</div>
                                            </div>
                                        @endforeach
                                        <div class="font-weight-medium d-flex mt-2">
                                            <div class="fw-bold">Total</div>
                                            <div class="ms-auto fw-bold">{{ $studentStatusTotal }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Student Registrations --}}
                    <div class="col-sm-6 col-md-6 col-lg-4 col-xl-3 col-xxl-2">
                        <div class="card card-sm">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <div class="font-weight-medium d-flex mb-2">
                                            <div class="fw-bold">Student Registrations</div>
                                        </div>
                                        <div class="d-flex">
                                            <div class="text-secondary">Today</div>
                                            <div class="text-secondary ms-auto">{{ $studentRegistrationsToday }}</div>
                                        </div>
                                        <div class="d-flex">
                                            <div class="text-secondary">This Week</div>
                                            <div class="text-secondary ms-auto">{{ $studentRegistrationsThisWeek }}</div>
                                        </div>
                                        <div class="d-flex">
                                            <div class="text-secondary">This Month</div>
                                            <div class="text-secondary ms-auto">{{ $studentRegistrationsThisMonth }}</div>
                                        </div>
                                        <div class="d-flex">
                                            <div class="text-secondary">This Year</div>
                                            <div class="text-secondary ms-auto">{{ $studentRegistrationsThisYear }}</div>
                                        </div>
                                        <div class="d-flex mt-2">
                                            <div class="fw-bold">Total</div>
                                            <div class="ms-auto fw-bold">{{ $studentRegistrationsAll }}</div>
                                        </div>
                                       
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Student Payments --}}
                    <div class="col-sm-6 col-md-6 col-lg-4 col-xl-3 col-xxl-2">
                        <div class="card card-sm">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <div class="font-weight-medium d-flex mb-2">
                                            <div class="fw-bold">Student Payments</div>
                                        </div>
                                        <div class="d-flex">
                                            <div class="text-secondary">Today</div>
                                            <div class="text-secondary ms-auto">{{ currency_format($installmentsPaidToday) }}</div>
                                        </div>
                                        <div class="d-flex">
                                            <div class="text-secondary">This Week</div>
                                            <div class="text-secondary ms-auto">{{ currency_format($installmentsPaidThisWeek) }}</div>
                                        </div>
                                        <div class="d-flex">
                                            <div class="text-secondary">This Month</div>
                                            <div class="text-secondary ms-auto">{{ currency_format($installmentsPaidThisMonth) }}</div>
                                        </div>
                                        <div class="d-flex">
                                            <div class="text-secondary">This Year</div>
                                            <div class="text-secondary ms-auto">{{ currency_format($installmentsPaidThisYear) }}</div>
                                        </div>
                                        <div class="d-flex mt-2">
                                            <div class="fw-bold">Total</div>
                                            <div class="ms-auto fw-bold">{{ currency_format($installmentsPaidAll) }}</div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Commissions --}}
                    <div class="col-sm-6 col-md-6 col-lg-4 col-xl-3 col-xxl-2">
                        <div class="card card-sm">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <div class="font-weight-medium d-flex mb-2">
                                            <div class="fw-bold">Commissions</div>
                                        </div>
                                        <div class="d-flex">
                                            <div class="text-secondary">Today</div>
                                            <div class="text-secondary ms-auto">{{ currency_format($commissionsPaidToday) }}</div>
                                        </div>
                                        <div class="d-flex">
                                            <div class="text-secondary">This Week</div>
                                            <div class="text-secondary ms-auto">{{ currency_format($commissionsPaidThisWeek) }}</div>
                                        </div>
                                        <div class="d-flex">
                                            <div class="text-secondary">This Month</div>
                                            <div class="text-secondary ms-auto">{{ currency_format($commissionsPaidThisMonth) }}</div>
                                        </div>
                                        <div class="d-flex">
                                            <div class="text-secondary">This Year</div>
                                            <div class="text-secondary ms-auto">{{ currency_format($commissionsPaidThisYear) }}</div>
                                        </div>
                                        <div class="d-flex mt-2">
                                            <div class="fw-bold">Total</div>
                                            <div class="ms-auto fw-bold">{{ currency_format($commissionsPaidAll) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Expenses --}}
                    <div class="col-sm-6 col-md-6 col-lg-4 col-xl-3 col-xxl-2">
                        <div class="card card-sm">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <div class="font-weight-medium d-flex mb-2">
                                            <div class="fw-bold">Expenses</div>
                                        </div>
                                        <div class="d-flex">
                                            <div class="text-secondary">Today</div>
                                            <div class="text-secondary ms-auto">{{ currency_format($expensesPaidToday) }}</div>
                                        </div>
                                        <div class="d-flex">
                                            <div class="text-secondary">This Week</div>
                                            <div class="text-secondary ms-auto">{{ currency_format($expensesPaidThisWeek) }}</div>
                                        </div>
                                        <div class="d-flex">
                                            <div class="text-secondary">This Month</div>
                                            <div class="text-secondary ms-auto">{{ currency_format($expensesPaidThisMonth) }}</div>
                                        </div>
                                        <div class="d-flex">
                                            <div class="text-secondary">This Year</div>
                                            <div class="text-secondary ms-auto">{{ currency_format($expensesPaidThisYear) }}</div>
                                        </div>
                                        <div class="d-flex mt-2">
                                            <div class="fw-bold">Total</div>
                                            <div class="ms-auto fw-bold">{{ currency_format($expensesPaidAll) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Incomes --}}
                    <div class="col-sm-6 col-md-6 col-lg-4 col-xl-3 col-xxl-2">
                        <div class="card card-sm">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <div class="font-weight-medium d-flex mb-2">
                                            <div class="fw-bold">Incomes</div>
                                        </div>
                                        <div class="d-flex">
                                            <div class="text-secondary">Today</div>
                                            <div class="text-secondary ms-auto">{{ currency_format($incomesPaidToday) }}</div>
                                        </div>
                                        <div class="d-flex">
                                            <div class="text-secondary">This Week</div>
                                            <div class="text-secondary ms-auto">{{ currency_format($incomesPaidThisWeek) }}</div>
                                        </div>
                                        <div class="d-flex">
                                            <div class="text-secondary">This Month</div>
                                            <div class="text-secondary ms-auto">{{ currency_format($incomesPaidThisMonth) }}</div>
                                        </div>
                                        <div class="d-flex">
                                            <div class="text-secondary">This Year</div>
                                            <div class="text-secondary ms-auto">{{ currency_format($incomesPaidThisYear) }}</div>
                                        </div>
                                        <div class="d-flex mt-2">
                                            <div class="fw-bold">Total</div>
                                            <div class="ms-auto fw-bold">{{ currency_format($incomesPaidAll) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="card card-sm">
                            <div class="card-body btn-default">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <div class="font-weight-medium d-flex">
                                            <div class="fw-bold">Cashier</div>
                                            <div class="ms-auto fw-bold">{{ currency_format($installmentsPaidAll+$incomesPaidAll-$expensesPaidAll-$commissionsPaidAll) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <hr class="mt-4 mb-2">

            @if(request('start_date') && request('end_date'))

            <div class="col-12">
                <div class="row row-cards">

                    {{-- Student Status Stats in Range --}}
                    <div class="col-sm-6 col-lg-2">
                        <div class="card card-sm">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <div class="font-weight-medium d-flex mb-2">
                                            <div class="fw-bold">Students Statuses (Filtered)</div>
                                        </div>
                                        @foreach ($studentStatusCountsInRange as $student)
                                            <div class="d-flex">
                                                <div class="text-secondary">{{ ucwords($student->userStatus->name) }}</div>
                                                <div class="text-secondary ms-auto">{{ $student->count }}</div>
                                            </div>
                                        @endforeach
                                        <div class="font-weight-medium d-flex mt-2">
                                            <div class="fw-bold">Total</div>
                                            <div class="ms-auto fw-bold">{{ $studentStatusTotalInRange }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Payments in Range --}}
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-sm">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <div class="font-weight-medium d-flex mb-2">
                                            <div class="fw-bold">Payments (Filtered)</div>
                                        </div>
                                        <div class="d-flex mt-2">
                                            <div class="text-secondary">Student Installments</div>
                                            <div class="text-secondary ms-auto">{{ currency_format($installmentsPaidInRange) }}</div>
                                        </div>
                                        <div class="d-flex mt-2">
                                            <div class="text-secondary">Commissions</div>
                                            <div class="text-secondary ms-auto">{{ currency_format($commissionsPaidInRange) }}</div>
                                        </div>
                                        <div class="d-flex mt-2">
                                            <div class="text-secondary">Expenses</div>
                                            <div class="text-secondary ms-auto">{{ currency_format($expensesPaidInRange) }}</div>
                                        </div>
                                        <div class="d-flex mt-2">
                                            <div class="text-secondary">Incomes</div>
                                            <div class="text-secondary ms-auto">{{ currency_format($incomesPaidInRange) }}</div>
                                        </div>
                                        <div class="font-weight-medium d-flex mt-2">
                                            <div class="fw-bold">Total</div>
                                            <div class="ms-auto fw-bold">{{ currency_format(($installmentsPaidInRange+$incomesPaidInRange)-($commissionsPaidInRange+$expensesPaidInRange)) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Upcoming Payments --}}
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-sm">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <div class="font-weight-medium d-flex mb-2">
                                            <div class="fw-bold">Upcoming Payments (Filtered)</div>
                                        </div>
                                        <div class="d-flex mb-2">
                                            <div class="text-secondary">Student Installments</div>
                                            <div class="text-secondary ms-auto">{{ currency_format($upcomingInstallmentsPaymentsInRange) }}</div>
                                        </div>
                                        <div class="d-flex mb-2">
                                            <div class="text-secondary">Commissions</div>
                                            <div class="ms-auto text-secondary">{{ currency_format($commissionsUpcomingInRange) }}</div>
                                        </div>
                                        <div class="d-flex mb-2">
                                            <div class="text-secondary">Expenses</div>
                                            <div class="ms-auto text-secondary">{{ currency_format($expensesUpcomingInRange) }}</div>
                                        </div>
                                        <div class="d-flex mb-2">
                                            <div class="text-secondary">Incomes</div>
                                            <div class="ms-auto text-secondary">{{ currency_format($upcomingIncomesPaymentsInRange) }}</div>
                                        </div>
                                        <div class="font-weight-medium d-flex mt-2">
                                            <div class="fw-bold">Total</div>
                                            <div class="ms-auto fw-bold">{{ currency_format($upcomingInstallmentsPaymentsInRange-$commissionsUpcomingInRange-$expensesUpcomingInRange+$upcomingIncomesPaymentsInRange) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            @endif

        </div>
    </div>
</div>
@endsection
