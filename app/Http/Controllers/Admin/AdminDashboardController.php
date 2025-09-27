<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Course;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Installment;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    
    public function index(Request $request)
    {
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? Carbon::now()->endOfMonth()->toDateString();

        // Student status 
        $studentStatusCounts = User::studentsOnly()
        ->select('user_status_id', DB::raw('COUNT(*) as count'))
        ->with('userStatus')
        ->groupBy('user_status_id')
        ->join('user_statuses', 'users.user_status_id', '=', 'user_statuses.id')
        ->orderByRaw("FIELD(user_statuses.name, 'graduated', 'withdrawn') ASC")
        ->orderBy('user_statuses.order')
        ->get();

        $studentStatusTotal = $studentStatusCounts->sum('count');

        $studentStatusCountsInRange = User::whereHas('roles', function ($q) {
            $q->where('name', 'student')
            ->where('user_roles.is_main', true);
        })
        ->whereBetween('created_at', [$startDate, $endDate])
        ->select('user_status_id', DB::raw('COUNT(*) as count'))
        ->groupBy('user_status_id')
        ->get();
        $studentStatusTotalInRange = $studentStatusCountsInRange->sum('count');

        // Dates
        $now = Carbon::now();
        $today = $now->copy()->startOfDay();
        $startOfWeek = $now->copy()->startOfWeek();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfYear = $now->copy()->startOfYear();

        // Installment Payment Stats
        $installmentsPaidToday = Installment::where('status', 'paid')->whereDate('paid_at', $today)->sum('amount');
        $installmentsPaidThisWeek = Installment::where('status', 'paid')->whereBetween('paid_at', [$startOfWeek, $now])->sum('amount');
        $installmentsPaidThisMonth = Installment::where('status', 'paid')->whereBetween('paid_at', [$startOfMonth, $now])->sum('amount');
        $installmentsPaidThisYear = Installment::where('status', 'paid')->whereBetween('paid_at', [$startOfYear, $now])->sum('amount');
        $installmentsPaidAll = Installment::where('status', 'paid')->sum('amount');
        $installmentsPaidInRange = Installment::where('status', 'paid')->whereBetween('paid_at', [$startDate, $endDate])->sum('amount');
        $upcomingInstallmentsPaymentsInRange = Installment::where('status', 'pending')->whereBetween('due_date', [$startDate, $endDate])->sum('amount');

        // Student Registration Stats
        $studentRegistrationsToday = User::studentsOnly()->whereDate('created_at', $today)->count();
        $studentRegistrationsThisWeek = User::studentsOnly()->whereBetween('created_at', [$startOfWeek, $now])->count();
        $studentRegistrationsThisMonth = User::studentsOnly()->whereBetween('created_at', [$startOfMonth, $now])->count();
        $studentRegistrationsThisYear = User::studentsOnly()->whereBetween('created_at', [$startOfYear, $now])->count();
        
        $studentRegistrationsAll = User::studentsOnly()->count();
        $studentRegistrationsInRange = User::whereHas('roles', function ($q) { $q->where('name', 'student')->where('user_roles.is_main', true); })->whereBetween('created_at', [$startDate, $endDate])->count();

        // Commission Payment Stats
        $commissionsPaidToday = Commission::where('status', 'paid')->whereDate('paid_at', $today)->sum('amount');
        $commissionsPaidThisWeek = Commission::where('status', 'paid')->whereBetween('paid_at', [$startOfWeek, $now])->sum('amount');
        $commissionsPaidThisMonth = Commission::where('status', 'paid')->whereBetween('paid_at', [$startOfMonth, $now])->sum('amount');
        $commissionsPaidThisYear = Commission::where('status', 'paid')->whereBetween('paid_at', [$startOfYear, $now])->sum('amount');
        $commissionsPaidAll = Commission::where('status', 'paid')->sum('amount');
        $commissionsPaidInRange = Commission::where('status', 'paid')->whereBetween('paid_at', [$startDate, $endDate])->sum('amount');
        $upcomingCommissionsPaymentsInRange = Commission::where('status', 'unpaid')->whereBetween('paid_at', [$startDate, $endDate])->sum('amount');

        // Expense Payment Stats
        $expensesPaidToday = Expense::where('status', 'paid')->whereDate('expense_date', $today)->sum(DB::raw('amount + transaction_fee'));
        $expensesPaidThisWeek = Expense::where('status', 'paid')->whereBetween('expense_date', [$startOfWeek, $now])->sum(DB::raw('amount + transaction_fee'));
        $expensesPaidThisMonth = Expense::where('status', 'paid')->whereBetween('expense_date', [$startOfMonth, $now])->sum(DB::raw('amount + transaction_fee'));
        $expensesPaidThisYear = Expense::where('status', 'paid')->whereBetween('expense_date', [$startOfYear, $now])->sum(DB::raw('amount + transaction_fee'));
        $expensesPaidAll = Expense::where('status', 'paid')->sum(DB::raw('amount + transaction_fee'));
        $expensesPaidInRange = Expense::where('status', 'paid')->whereBetween('expense_date', [$startDate, $endDate])->sum(DB::raw('amount + transaction_fee'));
        $upcomingExpensesPaymentsInRange = Expense::where('status', 0)->whereBetween('expense_date', [$startDate, $endDate])->sum(DB::raw('amount + transaction_fee'));
        
        // Income Payment Stats
        $incomesPaidToday = Income::where('status', 'paid')->whereDate('income_date', $today)->sum('amount');
        $incomesPaidThisWeek = Income::where('status', 'paid')->whereBetween('income_date', [$startOfWeek, $now])->sum('amount');
        $incomesPaidThisMonth = Income::where('status', 'paid')->whereBetween('income_date', [$startOfMonth, $now])->sum('amount');
        $incomesPaidThisYear = Income::where('status', 'paid')->whereBetween('income_date', [$startOfYear, $now])->sum('amount');
        $incomesPaidAll = Income::where('status', 'paid')->sum('amount');
        $incomesPaidInRange = Income::where('status', 'paid')->whereBetween('income_date', [$startDate, $endDate])->sum('amount');
        $upcomingIncomesPaymentsInRange = Income::where('status', 0)->whereBetween('income_date', [$startDate, $endDate])->sum('amount');

        // Paid expenses in date range
        $expensesPaidInRange = DB::table('expenses')
            ->where('status', 'paid')
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->sum('amount');

        // Upcoming expenses in date range
        $expensesUpcomingInRange = DB::table('expenses')
            ->where('status', 'unpaid')
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->sum('amount');

        // Paid commissions in selected range
        $commissionsPaidInRange = DB::table('commissions')
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->sum('amount');

        // Upcoming (unpaid) commissions scheduled in selected range
        $commissionsUpcomingInRange = DB::table('commissions')
            ->where('status', 'unpaid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->sum('amount');



        return view('admin.dashboard', compact(
            'studentStatusCounts',
            'studentStatusTotal',

            'installmentsPaidToday',
            'installmentsPaidThisWeek',
            'installmentsPaidThisMonth',
            'installmentsPaidThisYear',
            'installmentsPaidAll',
            
            'studentRegistrationsToday',
            'studentRegistrationsThisWeek',
            'studentRegistrationsThisMonth',
            'studentRegistrationsThisYear',
            'studentRegistrationsAll',

            'commissionsPaidToday',
            'commissionsPaidThisWeek',
            'commissionsPaidThisMonth',
            'commissionsPaidThisYear',
            'commissionsPaidAll',
            
            'expensesPaidToday',
            'expensesPaidThisWeek',
            'expensesPaidThisMonth',
            'expensesPaidThisYear',
            'expensesPaidAll',

            'incomesPaidToday',
            'incomesPaidThisWeek',
            'incomesPaidThisMonth',
            'incomesPaidThisYear',
            'incomesPaidAll',

            'studentStatusCountsInRange',
            'studentStatusTotalInRange',
            'studentRegistrationsInRange',
            'installmentsPaidInRange',
            'commissionsPaidInRange',
            'expensesPaidInRange',
            'incomesPaidInRange',
            
            'upcomingInstallmentsPaymentsInRange',
            'upcomingExpensesPaymentsInRange',
            'expensesUpcomingInRange',
            'commissionsPaidInRange',
            'commissionsUpcomingInRange',
            
            'upcomingIncomesPaymentsInRange',

            'startDate',
            'endDate'
        ));
    }

}
