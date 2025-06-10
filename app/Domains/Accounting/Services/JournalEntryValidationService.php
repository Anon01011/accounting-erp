<?php

namespace App\Domains\Accounting\Services;

use App\Domains\Accounting\Models\JournalEntry;
use App\Domains\Accounting\Models\ChartOfAccount;
use App\Domains\Accounting\Models\AccountingPeriod;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class JournalEntryValidationService
{
    private const MAX_AMOUNT_LIMIT = 999999999.99; // Maximum amount limit
    private const MAX_ENTRIES_PER_DAY = 1000; // Maximum entries per day

    public function validate(JournalEntry $journalEntry): array
    {
        $errors = [];

        // Validate future dates
        if ($journalEntry->entry_date->isFuture()) {
            $errors[] = 'Journal entry date cannot be in the future.';
        }

        // Validate accounting period
        if (!$this->isAccountingPeriodOpen($journalEntry->entry_date)) {
            $errors[] = 'The accounting period for this date is closed.';
        }

        // Validate amount limits
        if ($journalEntry->items->sum('debit') > self::MAX_AMOUNT_LIMIT) {
            $errors[] = 'Total debit amount exceeds the maximum limit of ' . number_format(self::MAX_AMOUNT_LIMIT, 2);
        }

        if ($journalEntry->items->sum('credit') > self::MAX_AMOUNT_LIMIT) {
            $errors[] = 'Total credit amount exceeds the maximum limit of ' . number_format(self::MAX_AMOUNT_LIMIT, 2);
        }

        // Validate account types and status
        foreach ($journalEntry->items as $item) {
            $account = $item->chartOfAccount;
            
            if (!$account) {
                $errors[] = "Account not found for line item.";
                continue;
            }

            if (!$account->is_active) {
                $errors[] = "Account {$account->name} is inactive.";
            }

            // Validate account type rules
            if ($item->debit > 0 && !$this->canDebit($account)) {
                $errors[] = "Account {$account->name} cannot be debited based on its type.";
            }

            if ($item->credit > 0 && !$this->canCredit($account)) {
                $errors[] = "Account {$account->name} cannot be credited based on its type.";
            }
        }

        // Validate daily entry limit
        if ($this->exceedsDailyEntryLimit($journalEntry)) {
            $errors[] = 'Daily entry limit exceeded.';
        }

        return $errors;
    }

    private function isAccountingPeriodOpen(Carbon $date): bool
    {
        return AccountingPeriod::where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->where('status', 'open')
            ->exists();
    }

    private function canDebit(ChartOfAccount $account): bool
    {
        // Assets and Expenses can be debited
        return in_array($account->type, ['asset', 'expense']);
    }

    private function canCredit(ChartOfAccount $account): bool
    {
        // Liabilities, Equity, and Revenue can be credited
        return in_array($account->type, ['liability', 'equity', 'revenue']);
    }

    private function exceedsDailyEntryLimit(JournalEntry $journalEntry): bool
    {
        $count = JournalEntry::whereDate('entry_date', $journalEntry->entry_date)
            ->count();

        return $count >= self::MAX_ENTRIES_PER_DAY;
    }
} 