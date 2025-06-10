<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetDetail extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'account_id',
        'serial_number',
        'purchase_date',
        'purchase_price',
        'warranty_expiry',
        'depreciation_method',
        'depreciation_rate',
        'useful_life',
        'location',
        'condition',
        'notes',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiry' => 'date',
        'purchase_price' => 'decimal:2',
        'depreciation_rate' => 'decimal:2',
        'useful_life' => 'integer'
    ];

    // Relationships
    public function account(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Accessors
    public function getAgeAttribute(): int
    {
        return $this->purchase_date->diffInYears(now());
    }

    public function getDepreciationAmountAttribute(): float
    {
        return $this->account->getDepreciationAmount();
    }

    public function getNetBookValueAttribute(): float
    {
        return $this->account->getNetBookValue();
    }

    public function getDepreciationStatusAttribute(): string
    {
        if ($this->age >= $this->useful_life) {
            return 'Fully Depreciated';
        }
        return 'Depreciating';
    }
} 