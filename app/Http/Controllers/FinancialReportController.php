<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
                ->groupBy('type');

            // Calculate balances for each account
            $balances = [];
            foreach ($accounts as $type => $typeAccounts) {
                foreach ($typeAccounts as $account) {
                    $balance = $this->calculateAccountBalance($account->id, $date);
                    $balances[$account->id] = $balance;
                }
            }

            return view('financial-reports.balance-sheet', compact('accounts', 'balances', 'date'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error generating balance sheet: ' . $e->getMessage());
        }
    }

    public function incomeStatement(Request $request)
    {
        try {
            $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
            $endDate = $request->get('end_date', now()->format('Y-m-d'));

            // Get revenue and expense accounts
            $revenueAccounts = ChartOfAccount::where('type', 'Revenue')
                ->where('is_active', true)
                ->orderBy('code')
                ->get();

            $expenseAccounts = ChartOfAccount::where('type', 'Expense')
                ->where('is_active', true)
                ->orderBy('code')
                ->get();

            // Calculate balances
            $revenueBalances = [];
            $expenseBalances = [];

            foreach ($revenueAccounts as $account) {
                $revenueBalances[$account->id] = $this->calculateAccountBalance($account->id, $endDate, $startDate);
            }

            foreach ($expenseAccounts as $account) {
                $expenseBalances[$account->id] = $this->calculateAccountBalance($account->id, $endDate, $startDate);
            }

            return view('financial-reports.income-statement', compact(
                'revenueAccounts',
                'expenseAccounts',
                'revenueBalances',
                'expenseBalances',
                'startDate',
                'endDate'
            ));
        } catch (\Exception $e) {
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
            foreach ($accounts as $account) {
                $balance = $this->calculateAccountBalance($account->id, $date);
                $balances[$account->id] = $balance;
            }

            return view('financial-reports.trial-balance', compact('accounts', 'balances', 'date'));
        } catch (\Exception $e) {
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
} 