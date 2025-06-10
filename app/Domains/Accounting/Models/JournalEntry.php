<?php

namespace App\Domains\Accounting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\DB;

class JournalEntry extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'account_id',
        'type', // debit or credit
        'amount',
        'description',
        'reference_type',
        'reference_id',
        'transaction_date',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'datetime',
    ];

    // Relationships
    public function account(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
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
    public function scopeDebit($query)
    {
        return $query->where('type', 'debit');
    }

    public function scopeCredit($query)
    {
        return $query->where('type', 'credit');
    }

    public function scopeOfDate($query, $date)
    {
        return $query->whereDate('transaction_date', $date);
    }

    public function scopeOfDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    // Helper Methods
    public static function createEntry($accountId, $type, $amount, $description, $reference = null, $transactionDate = null)
    {
        return static::create([
            'account_id' => $accountId,
            'type' => $type,
            'amount' => $amount,
            'description' => $description,
            'reference_type' => $reference ? get_class($reference) : null,
            'reference_id' => $reference ? $reference->id : null,
            'transaction_date' => $transactionDate ?? now(),
            'created_by' => auth()->id()
        ]);
    }

    public static function createDoubleEntry($debitAccountId, $creditAccountId, $amount, $description, $reference = null, $transactionDate = null)
    {
        DB::transaction(function () use ($debitAccountId, $creditAccountId, $amount, $description, $reference, $transactionDate) {
            // Create debit entry
            static::createEntry($debitAccountId, 'debit', $amount, $description, $reference, $transactionDate);
            
            // Create credit entry
            static::createEntry($creditAccountId, 'credit', $amount, $description, $reference, $transactionDate);
        });
    }
} 