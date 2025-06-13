<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetTransaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'asset_id',
        'type',
        'transaction_type',
        'amount',
        'date',
        'description',
        'reference_type',
        'reference_id',
        'journal_entry_id',
        'tax_related',
        'tax_amount',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'tax_related' => 'boolean'
    ];

    // Constants
    const TYPES = [
        'acquisition' => 'Acquisition',
        'disposal' => 'Disposal',
        'depreciation' => 'Depreciation',
        'revaluation' => 'Revaluation',
        'impairment' => 'Impairment',
        'maintenance' => 'Maintenance',
        'repair' => 'Repair',
        'upgrade' => 'Upgrade',
        'transfer' => 'Transfer'
    ];

    const TRANSACTION_TYPES = [
        'debit' => 'Debit',
        'credit' => 'Credit'
    ];

    // Transaction Types
    const TYPE_PURCHASE = 'purchase';
    const TYPE_DEPRECIATION = 'depreciation';
    const TYPE_DISPOSAL = 'disposal';
    const TYPE_MAINTENANCE = 'maintenance';
    const TYPE_IMPAIRMENT = 'impairment';
    const TYPE_REVALUATION = 'revaluation';

    // Relationships
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'reference_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeDepreciation($query)
    {
        return $query->where('type', self::TYPE_DEPRECIATION);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    // Methods
    public function createJournalEntry(): JournalEntry
    {
        $journalEntry = new JournalEntry([
            'entry_date' => $this->date,
            'reference_no' => 'AT-' . date('Ymd') . '-' . str_pad($this->id, 4, '0', STR_PAD_LEFT),
            'description' => 'Asset ' . $this->type . ' - ' . $this->asset->name,
            'status' => 'posted',
            'created_by' => $this->created_by,
            'posted_by' => $this->created_by,
            'posted_at' => now()
        ]);

        $journalEntry->save();

        // Create journal entry items based on transaction type
        switch ($this->type) {
            case self::TYPE_PURCHASE:
                $this->createPurchaseJournalItems($journalEntry);
                break;
            case self::TYPE_DEPRECIATION:
                $this->createDepreciationJournalItems($journalEntry);
                break;
            case self::TYPE_DISPOSAL:
                $this->createDisposalJournalItems($journalEntry);
                break;
        }

        $this->update([
            'reference_type' => 'journal_entry',
            'reference_id' => $journalEntry->id
        ]);

        return $journalEntry;
    }

    private function createPurchaseJournalItems(JournalEntry $journalEntry)
    {
        // Debit Asset Account
        $journalEntry->items()->create([
            'chart_of_account_id' => $this->asset->chartOfAccount->id,
            'debit' => $this->amount,
            'credit' => 0
        ]);

        // Credit Bank/Cash Account
        $journalEntry->items()->create([
            'chart_of_account_id' => config('accounting.default_accounts.bank'),
            'debit' => 0,
            'credit' => $this->amount
        ]);
    }

    private function createDepreciationJournalItems(JournalEntry $journalEntry)
    {
        // Debit Depreciation Expense
        $journalEntry->items()->create([
            'chart_of_account_id' => config('accounting.default_accounts.depreciation_expense'),
            'debit' => $this->amount,
            'credit' => 0
        ]);

        // Credit Accumulated Depreciation
        $journalEntry->items()->create([
            'chart_of_account_id' => $this->asset->chartOfAccount->id,
            'debit' => 0,
            'credit' => $this->amount
        ]);
    }

    private function createDisposalJournalItems(JournalEntry $journalEntry)
    {
        $assetValue = $this->asset->getCurrentValue();
        $accumulatedDepreciation = $this->asset->getAccumulatedDepreciation();

        // Debit Accumulated Depreciation
        $journalEntry->items()->create([
            'chart_of_account_id' => $this->asset->chartOfAccount->id,
            'debit' => $accumulatedDepreciation,
            'credit' => 0
        ]);

        // Debit/Credit Asset Account
        $journalEntry->items()->create([
            'chart_of_account_id' => $this->asset->chartOfAccount->id,
            'debit' => 0,
            'credit' => $this->asset->purchase_price
        ]);

        // Debit/Credit Gain/Loss on Disposal
        if ($this->amount != $assetValue) {
            $gainLossAccount = $this->amount > $assetValue 
                ? config('accounting.default_accounts.gain_on_disposal')
                : config('accounting.default_accounts.loss_on_disposal');

            $journalEntry->items()->create([
                'chart_of_account_id' => $gainLossAccount,
                'debit' => $this->amount > $assetValue ? 0 : ($assetValue - $this->amount),
                'credit' => $this->amount > $assetValue ? ($this->amount - $assetValue) : 0
            ]);
        }
    }

    public function getTotalAmount()
    {
        return $this->amount + ($this->tax_related ? $this->tax_amount : 0);
    }

    public function isTaxable()
    {
        return $this->tax_related && $this->tax_amount > 0;
    }

    public function getTransactionTypeLabel()
    {
        return self::TRANSACTION_TYPES[$this->transaction_type] ?? $this->transaction_type;
    }

    public function getTypeLabel()
    {
        return self::TYPES[$this->type] ?? $this->type;
    }
} 