<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JournalEntry;
use App\Models\ChartOfAccount;
use App\Models\Employee;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Invoice;
use App\Models\PurchaseBill;
use App\Models\Warehouse;
use App\Models\Payroll;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Revenue (last 6 months)
        $revenueAccounts = ChartOfAccount::where('type_code', '04')->pluck('id');
        $revenueData = [
            'labels' => [],
            'data' => []
        ];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $label = $month->format('M');
            $amount = JournalEntry::whereMonth('entry_date', $month->month)
                ->whereYear('entry_date', $month->year)
                ->whereHas('items', function($query) use ($revenueAccounts) {
                    $query->whereIn('chart_of_account_id', $revenueAccounts);
                })
                ->withSum(['items as total_credit' => function($query) use ($revenueAccounts) {
                    $query->whereIn('chart_of_account_id', $revenueAccounts);
                }], 'credit')
                ->get()
                ->sum('total_credit');
            $revenueData['labels'][] = $label;
            $revenueData['data'][] = $amount;
        }

        // Expenses (current month breakdown)
        $expenseAccounts = ChartOfAccount::where('type_code', '05')->pluck('id');
        $expenseData = [
            'labels' => ['Salaries', 'Rent', 'Utilities', 'Marketing', 'Other'],
            'data' => [],
            'colors' => ['#01657F', '#0284c7', '#0ea5e9', '#38bdf8', '#7dd3fc']
        ];
        // For demo, sum all expenses for current month
        $month = Carbon::now();
        $totalExpense = JournalEntry::whereMonth('entry_date', $month->month)
            ->whereYear('entry_date', $month->year)
            ->whereHas('items', function($query) use ($expenseAccounts) {
                $query->whereIn('chart_of_account_id', $expenseAccounts);
            })
            ->withSum(['items as total_debit' => function($query) use ($expenseAccounts) {
                $query->whereIn('chart_of_account_id', $expenseAccounts);
            }], 'debit')
            ->get()
            ->sum('total_debit');
        $expenseData['data'] = [
            $totalExpense * 0.4, // Salaries
            $totalExpense * 0.2, // Rent
            $totalExpense * 0.15, // Utilities
            $totalExpense * 0.15, // Marketing
            $totalExpense * 0.1, // Other
        ];

        // Cash flow (last 6 months)
        $cashAccounts = ChartOfAccount::where('class_code', '10')->pluck('id'); // Cash and Cash Equivalents
        $cashFlowData = [
            'labels' => [],
            'income' => [],
            'expenses' => []
        ];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $label = $month->format('M');
            $income = JournalEntry::whereMonth('entry_date', $month->month)
                ->whereYear('entry_date', $month->year)
                ->whereHas('items', function($query) use ($cashAccounts) {
                    $query->whereIn('chart_of_account_id', $cashAccounts);
                })
                ->withSum(['items as total_credit' => function($query) use ($cashAccounts) {
                    $query->whereIn('chart_of_account_id', $cashAccounts);
                }], 'credit')
                ->get()
                ->sum('total_credit');
            $expenses = JournalEntry::whereMonth('entry_date', $month->month)
                ->whereYear('entry_date', $month->year)
                ->whereHas('items', function($query) use ($cashAccounts) {
                    $query->whereIn('chart_of_account_id', $cashAccounts);
                })
                ->withSum(['items as total_debit' => function($query) use ($cashAccounts) {
                    $query->whereIn('chart_of_account_id', $cashAccounts);
                }], 'debit')
                ->get()
                ->sum('total_debit');
            $cashFlowData['labels'][] = $label;
            $cashFlowData['income'][] = $income;
            $cashFlowData['expenses'][] = $expenses;
        }

        // HR
        $totalEmployees = Employee::count();
        $payrollThisMonth = Payroll::whereMonth('payroll_period', now()->month)
            ->whereYear('payroll_period', now()->year)
            ->sum('net_salary');

        // Inventory
        $totalProducts = Product::count();
        $stockValue = Product::sum(DB::raw('current_stock * purchase_price'));
        $lowStock = Product::where('current_stock', '<', 'min_stock')->count();

        // Sales
        $salesThisMonth = Invoice::whereMonth('invoice_date', now()->month)
            ->whereYear('invoice_date', now()->year)
            ->sum('total_amount');
        $topCustomers = Customer::withSum(['invoices as total_sales' => function($q) {
            $q->whereMonth('invoice_date', now()->month)->whereYear('invoice_date', now()->year);
        }], 'total_amount')->orderByDesc('total_sales')->take(5)->get();

        // Purchases
        $purchasesThisMonth = PurchaseBill::whereMonth('bill_date', now()->month)
            ->whereYear('bill_date', now()->year)
            ->sum('total_amount');
        $topSuppliers = Supplier::withSum(['purchaseBills as total_purchases' => function($q) {
            $q->whereMonth('bill_date', now()->month)->whereYear('bill_date', now()->year);
        }], 'total_amount')->orderByDesc('total_purchases')->take(5)->get();

        return view('dashboard', compact(
            'revenueData',
            'expenseData',
            'cashFlowData',
            'totalEmployees',
            'payrollThisMonth',
            'totalProducts',
            'stockValue',
            'lowStock',
            'salesThisMonth',
            'topCustomers',
            'purchasesThisMonth',
            'topSuppliers'
        ));
    }
} 