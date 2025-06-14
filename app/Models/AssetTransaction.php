<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class AssetTransaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'asset_id',
        'type',
        'purchase_type',
        'transaction_type',
        'amount',
        'date',
        'description',
        'reference',
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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            Log::info('AssetTransaction creating:', $transaction->toArray());
        });

        static::created(function ($transaction) {
            Log::info('AssetTransaction created:', $transaction->toArray());
        });

        static::updating(function ($transaction) {
            Log::info('AssetTransaction updating (original):', $transaction->getOriginal());
            Log::info('AssetTransaction updating (changes):', $transaction->getDirty());
        });

        static::updated(function ($transaction) {
            Log::info('AssetTransaction updated:', $transaction->toArray());
        });
    }

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
            case self::TYPE_MAINTENANCE:
                $this->createMaintenanceJournalItems($journalEntry);
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
        // Get the depreciation expense account
        $depreciationExpenseAccount = ChartOfAccount::where('account_code', config('accounting.default_accounts.depreciation_expense'))->first();
        if (!$depreciationExpenseAccount) {
            throw new \Exception('Depreciation expense account not found. Please configure it in the accounting settings.');
        }

        // Get the accumulated depreciation account
        $accumulatedDepreciationAccount = $this->asset->chartOfAccount;
        if (!$accumulatedDepreciationAccount) {
            throw new \Exception('Asset account not found.');
        }

        // Debit Depreciation Expense
        $journalEntry->items()->create([
            'chart_of_account_id' => $depreciationExpenseAccount->id,
            'debit' => $this->amount,
            'credit' => 0,
            'description' => 'Depreciation expense for ' . $this->asset->name,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by
        ]);

        // Credit Accumulated Depreciation
        $journalEntry->items()->create([
            'chart_of_account_id' => $accumulatedDepreciationAccount->id,
            'debit' => 0,
            'credit' => $this->amount,
            'description' => 'Accumulated depreciation for ' . $this->asset->name,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by
        ]);

        // Log the journal entry items creation
        Log::info('Depreciation journal entry items created', [
            'journal_entry_id' => $journalEntry->id,
            'asset_id' => $this->asset_id,
            'amount' => $this->amount
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

    private function createMaintenanceJournalItems(JournalEntry $journalEntry)
    {
        // Debit Maintenance Expense
        $journalEntry->items()->create([
            'chart_of_account_id' => config('accounting.default_accounts.maintenance_expense'),
            'debit' => $this->amount,
            'credit' => 0
        ]);

        // Credit Cash/Bank Account
        $journalEntry->items()->create([
            'chart_of_account_id' => config('accounting.default_accounts.bank'), // or cash account
            'debit' => 0,
            'credit' => $this->amount
        ]);
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