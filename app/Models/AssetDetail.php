<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\AssetCalculations;

class AssetDetail extends Model
{
    use HasFactory, SoftDeletes, AssetCalculations;

    protected $fillable = [
        'account_id',
        'serial_number',
        'purchase_date',
        'purchase_price',
        'warranty_expiry',
        'depreciation_method',
        'depreciation_rate',
        'useful_life',
        'total_units',
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
        'total_units' => 'integer'
    ];

    // Depreciation Methods
    const METHOD_STRAIGHT_LINE = 'straight_line';
    const METHOD_DECLINING_BALANCE = 'declining_balance';
    const METHOD_SUM_OF_YEARS = 'sum_of_years';
    const METHOD_DOUBLE_DECLINING = 'double_declining';
    const METHOD_UNITS_OF_PRODUCTION = 'units_of_production';

    // Asset Conditions
    const CONDITION_NEW = 'new';
    const CONDITION_GOOD = 'good';
    const CONDITION_FAIR = 'fair';
    const CONDITION_POOR = 'poor';
    const CONDITION_CRITICAL = 'critical';

    // Relationships
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getDepreciationMethods(): array
    {
        return [
            self::METHOD_STRAIGHT_LINE => 'Straight Line',
            self::METHOD_DECLINING_BALANCE => 'Declining Balance',
            self::METHOD_SUM_OF_YEARS => 'Sum of Years',
            self::METHOD_DOUBLE_DECLINING => 'Double Declining',
            self::METHOD_UNITS_OF_PRODUCTION => 'Units of Production'
        ];
    }

    public function getConditions(): array
    {
        return [
            self::CONDITION_NEW => 'New',
            self::CONDITION_GOOD => 'Good',
            self::CONDITION_FAIR => 'Fair',
            self::CONDITION_POOR => 'Poor',
            self::CONDITION_CRITICAL => 'Critical'
        ];
    }

    public function isWarrantyValid(): bool
    {
        return $this->warranty_expiry && $this->warranty_expiry->isFuture();
    }

    public function getWarrantyDaysRemaining(): int
    {
        if (!$this->warranty_expiry) {
            return 0;
        }
        return max(0, now()->diffInDays($this->warranty_expiry, false));
    }

    public function getDepreciationStatus(): string
    {
        $percentage = $this->getDepreciationPercentage();
        
        if ($percentage >= 100) {
            return 'Fully Depreciated';
        } elseif ($percentage >= 75) {
            return 'Mostly Depreciated';
        } elseif ($percentage >= 50) {
            return 'Half Depreciated';
        } elseif ($percentage >= 25) {
            return 'Partially Depreciated';
        } else {
            return 'Newly Acquired';
        }
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
} 