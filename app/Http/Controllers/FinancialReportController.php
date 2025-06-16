<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class FinancialReportController extends Controller
{
    public function balanceSheet(Request $request)
    {
        try {
            $date = $request->get('date', now()->format('Y-m-d'));

            // Get all accounts grouped by type
            $accounts = ChartOfAccount::where('is_active', true)
                ->orderBy('code')
                ->get()
                ->groupBy('type_code');

            // Calculate balances for each account
            $balances = [];
            $totals = [
                'ASSET' => 0,
                'LIABILITY' => 0,
                'EQUITY' => 0
            ];

            foreach ($accounts as $type => $typeAccounts) {
                foreach ($typeAccounts as $account) {
                    $balance = $this->calculateAccountBalance($account->id, $date);
                    $balances[$account->id] = $balance;
                    if (in_array($type, ['ASSET', 'LIABILITY', 'EQUITY'])) {
                        $totals[$type] += $balance;
                    }
                }
            }

            // Calculate net income for equity
            $netIncome = $this->calculateNetIncome($date);
            $totals['EQUITY'] += $netIncome;

            return view('financial-reports.balance-sheet', compact('accounts', 'balances', 'date', 'totals', 'netIncome'));
        } catch (\Exception $e) {
            \Log::error('Balance Sheet Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error generating balance sheet: ' . $e->getMessage());
        }
    }

    public function incomeStatement(Request $request)
    {
        try {
            $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
            $endDate = $request->get('end_date', now()->format('Y-m-d'));

            // Get revenue and expense accounts
            $revenueAccounts = ChartOfAccount::where('type_code', 'REVENUE')
                ->where('is_active', true)
                ->orderBy('code')
                ->get();

            $expenseAccounts = ChartOfAccount::where('type_code', 'EXPENSE')
                ->where('is_active', true)
                ->orderBy('code')
                ->get();

            // Calculate balances
            $revenueBalances = [];
            $expenseBalances = [];
            $totalRevenue = 0;
            $totalExpenses = 0;

            foreach ($revenueAccounts as $account) {
                $balance = $this->calculateAccountBalance($account->id, $endDate, $startDate);
                $revenueBalances[$account->id] = $balance;
                $totalRevenue += $balance;
            }

            foreach ($expenseAccounts as $account) {
                $balance = $this->calculateAccountBalance($account->id, $endDate, $startDate);
                $expenseBalances[$account->id] = $balance;
                $totalExpenses += $balance;
            }

            $netIncome = $totalRevenue - $totalExpenses;

            return view('financial-reports.income-statement', compact(
                'revenueAccounts',
                'expenseAccounts',
                'revenueBalances',
                'expenseBalances',
                'startDate',
                'endDate',
                'totalRevenue',
                'totalExpenses',
                'netIncome'
            ));
        } catch (\Exception $e) {
            \Log::error('Income Statement Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error generating income statement: ' . $e->getMessage());
        }
    }

    public function trialBalance(Request $request)
    {
        try {
            $date = $request->get('date', now()->format('Y-m-d'));

            $accounts = ChartOfAccount::where('is_active', true)
                ->orderBy('code')
                ->get();

            $balances = [];
            $totalDebits = 0;
            $totalCredits = 0;

            foreach ($accounts as $account) {
                $debits = $this->calculateAccountDebits($account->id, $date);
                $credits = $this->calculateAccountCredits($account->id, $date);
                $balance = $debits - $credits;
                
                $balances[$account->id] = [
                    'debits' => $debits,
                    'credits' => $credits,
                    'balance' => $balance
                ];

                $totalDebits += $debits;
                $totalCredits += $credits;
            }

            return view('financial-reports.trial-balance', compact('accounts', 'balances', 'date', 'totalDebits', 'totalCredits'));
        } catch (\Exception $e) {
            \Log::error('Trial Balance Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error generating trial balance: ' . $e->getMessage());
        }
    }

    private function calculateAccountBalance($accountId, $endDate, $startDate = null)
    {
        $query = JournalEntry::join('journal_entry_items', 'journal_entries.id', '=', 'journal_entry_items.journal_entry_id')
            ->where('journal_entries.status', 'posted')
            ->where('journal_entry_items.chart_of_account_id', $accountId)
            ->where('journal_entries.transaction_date', '<=', $endDate);

        if ($startDate) {
            $query->where('journal_entries.transaction_date', '>=', $startDate);
        }

        $debits = (clone $query)->sum('journal_entry_items.debit');
        $credits = (clone $query)->sum('journal_entry_items.credit');

        return $debits - $credits;
    }

    private function calculateAccountDebits($accountId, $date)
    {
        return JournalEntry::join('journal_entry_items', 'journal_entries.id', '=', 'journal_entry_items.journal_entry_id')
            ->where('journal_entries.status', 'posted')
            ->where('journal_entry_items.chart_of_account_id', $accountId)
            ->where('journal_entries.transaction_date', '<=', $date)
            ->sum('journal_entry_items.debit');
    }

    private function calculateAccountCredits($accountId, $date)
    {
        return JournalEntry::join('journal_entry_items', 'journal_entries.id', '=', 'journal_entry_items.journal_entry_id')
            ->where('journal_entries.status', 'posted')
            ->where('journal_entry_items.chart_of_account_id', $accountId)
            ->where('journal_entries.transaction_date', '<=', $date)
            ->sum('journal_entry_items.credit');
    }

    private function calculateNetIncome($date)
    {
        $startDate = Carbon::parse($date)->startOfYear()->format('Y-m-d');
        
        $revenueAccounts = ChartOfAccount::where('type', 'Revenue')
            ->where('is_active', true)
            ->pluck('id');
            
        $expenseAccounts = ChartOfAccount::where('type', 'Expense')
            ->where('is_active', true)
            ->pluck('id');

        $totalRevenue = JournalEntry::join('journal_entry_items', 'journal_entries.id', '=', 'journal_entry_items.journal_entry_id')
            ->where('journal_entries.status', 'posted')
            ->whereIn('journal_entry_items.chart_of_account_id', $revenueAccounts)
            ->whereBetween('journal_entries.transaction_date', [$startDate, $date])
            ->sum(DB::raw('journal_entry_items.debit - journal_entry_items.credit'));

        $totalExpenses = JournalEntry::join('journal_entry_items', 'journal_entries.id', '=', 'journal_entry_items.journal_entry_id')
            ->where('journal_entries.status', 'posted')
            ->whereIn('journal_entry_items.chart_of_account_id', $expenseAccounts)
            ->whereBetween('journal_entries.transaction_date', [$startDate, $date])
            ->sum(DB::raw('journal_entry_items.debit - journal_entry_items.credit'));

        return $totalRevenue - $totalExpenses;
    }
} 