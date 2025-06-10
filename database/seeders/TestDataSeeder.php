<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JournalEntry;
use App\Models\JournalEntryItem;
use App\Models\ChartOfAccount;
use App\Models\User;
use Carbon\Carbon;

class TestDataSeeder extends Seeder
{
    public function run()
    {
        // Get or create a user for the entries
        $user = User::first() ?? User::factory()->create();

        // Get some accounts
        $cashAccount = ChartOfAccount::where('class_code', '10')->first();
        $salesAccount = ChartOfAccount::where('class_code', '10')->where('type_code', '04')->first();
        $expenseAccount = ChartOfAccount::where('class_code', '10')->where('type_code', '05')->first();

        if (!$cashAccount || !$salesAccount || !$expenseAccount) {
            $this->command->error('Required accounts not found. Please run AccountingSeeder first.');
            return;
        }

        // Create a sales journal entry
        $salesEntry = JournalEntry::create([
            'entry_date' => Carbon::now(),
            'reference_no' => 'JE-' . date('Ymd') . '-001',
            'description' => 'Monthly sales entry',
            'status' => 'posted',
            'created_by' => $user->id,
            'posted_by' => $user->id,
            'posted_at' => Carbon::now(),
        ]);

        JournalEntryItem::create([
            'journal_entry_id' => $salesEntry->id,
            'chart_of_account_id' => $cashAccount->id,
            'debit' => 5000.00,
            'credit' => 0,
            'description' => 'Cash received from sales',
            'created_by' => $user->id,
        ]);

        JournalEntryItem::create([
            'journal_entry_id' => $salesEntry->id,
            'chart_of_account_id' => $salesAccount->id,
            'debit' => 0,
            'credit' => 5000.00,
            'description' => 'Sales revenue',
            'created_by' => $user->id,
        ]);

        // Create an expense journal entry
        $expenseEntry = JournalEntry::create([
            'entry_date' => Carbon::now(),
            'reference_no' => 'JE-' . date('Ymd') . '-002',
            'description' => 'Monthly expense entry',
            'status' => 'posted',
            'created_by' => $user->id,
            'posted_by' => $user->id,
            'posted_at' => Carbon::now(),
        ]);

        JournalEntryItem::create([
            'journal_entry_id' => $expenseEntry->id,
            'chart_of_account_id' => $expenseAccount->id,
            'debit' => 2000.00,
            'credit' => 0,
            'description' => 'Monthly expenses',
            'created_by' => $user->id,
        ]);

        JournalEntryItem::create([
            'journal_entry_id' => $expenseEntry->id,
            'chart_of_account_id' => $cashAccount->id,
            'debit' => 0,
            'credit' => 2000.00,
            'description' => 'Cash paid for expenses',
            'created_by' => $user->id,
        ]);
    }
} 