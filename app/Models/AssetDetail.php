<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\AssetCalculations;
use Carbon\Carbon;

class AssetDetail extends Model
{
    use HasFactory, SoftDeletes, AssetCalculations;

    protected $fillable = [
        'asset_id',
        'serial_number',
        'purchase_date',
        'purchase_price',
        'supplier',
        'warranty_period',
        'warranty_expiry',
        'depreciation_method',
        'depreciation_rate',
        'useful_life',
        'residual_value',
        'revaluation_frequency',
        'depreciation_start_date',
        'location',
        'condition',
        'notes',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiry' => 'date',
        'depreciation_start_date' => 'date',
        'purchase_price' => 'decimal:2',
        'depreciation_rate' => 'decimal:2',
        'residual_value' => 'decimal:2'
    ];

    // Constants
    const DEPRECIATION_METHODS = [
        'straight_line' => 'Straight Line',
        'declining_balance' => 'Declining Balance',
        'sum_of_years' => 'Sum of Years',
        'double_declining' => 'Double Declining',
        'units_of_production' => 'Units of Production'
    ];

    const CONDITIONS = [
        'new' => 'New',
        'good' => 'Good',
        'fair' => 'Fair',
        'poor' => 'Poor'
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

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getDepreciationMethods(): array
    {
        return self::DEPRECIATION_METHODS;
    }

    public function getConditions(): array
    {
        return self::CONDITIONS;
    }

    public function isWarrantyExpired(): bool
    {
        return $this->warranty_expiry && $this->warranty_expiry < now();
    }

    public function getWarrantyStatus(): string
    {
        if (!$this->warranty_expiry) {
            return 'No warranty';
        }

        if ($this->isWarrantyExpired()) {
            return 'Expired';
        }

        return 'Active';
    }

    public function getDepreciationStatus(): string
    {
        if (!$this->depreciation_start_date) {
            return 'Not Started';
        }

        if ($this->depreciation_end_date && now()->isAfter($this->depreciation_end_date)) {
            return 'Completed';
        }

        return 'In Progress';
    }

    public function getNextRevaluationDate()
    {
        if (!$this->last_revaluation_date || !$this->revaluation_frequency) {
            return null;
        }

        $frequency = $this->revaluation_frequency;
        $lastDate = $this->last_revaluation_date;

        switch ($frequency) {
            case 'monthly':
                return $lastDate->addMonth();
            case 'quarterly':
                return $lastDate->addMonths(3);
            case 'semi_annual':
                return $lastDate->addMonths(6);
            case 'annual':
                return $lastDate->addYear();
            case 'biennial':
                return $lastDate->addYears(2);
            case 'triennial':
                return $lastDate->addYears(3);
            default:
                return null;
        }
    }

    public function isRevaluationDue()
    {
        if (!$this->next_revaluation_date) {
            return false;
        }

        return now()->isAfter($this->next_revaluation_date);
    }

    public function getRemainingLife(): int
    {
        if (!$this->depreciation_start_date || !$this->useful_life) {
            return 0;
        }

        $startDate = $this->depreciation_start_date;
        $endDate = $this->depreciation_end_date ?? $startDate->copy()->addYears($this->useful_life);
        
        if (now()->isAfter($endDate)) {
            return 0;
        }

        return now()->diffInYears($endDate);
    }

    // Accessors
    public function getAgeAttribute(): int
    {
        return $this->purchase_date->diffInYears(now());
    }

    public function getDepreciationAmountAttribute(): float
    {
        return $this->asset->getDepreciationAmount();
    }

    public function getNetBookValueAttribute(): float
    {
        return $this->asset->getNetBookValue();
    }

    public function getCurrentValueAttribute(): float
    {
        return $this->asset->getCurrentValue();
    }

    public function getAccumulatedDepreciationAttribute(): float
    {
        return $this->asset->getAccumulatedDepreciation();
    }

    public function getDepreciationPercentageAttribute(): float
    {
        if ($this->purchase_price <= 0) {
            return 0;
        }
        return min(100, ($this->accumulated_depreciation / $this->purchase_price) * 100);
    }

    public function getRemainingLifeAttribute(): int
    {
        return $this->getRemainingLife();
    }

    public function getNextMaintenanceDateAttribute(): ?Carbon
    {
        $lastMaintenance = $this->asset->maintenanceRecords()
            ->latest()
            ->first();

        if (!$lastMaintenance) {
            return now()->addMonths(3); // Default to 3 months if no maintenance history
        }

        return $lastMaintenance->next_maintenance_date;
    }

    public function getMaintenanceStatusAttribute(): string
    {
        if (!$this->next_maintenance_date) {
            return 'No Maintenance Scheduled';
        }

        if ($this->next_maintenance_date->isPast()) {
            return 'Overdue';
        }

        $daysUntilMaintenance = now()->diffInDays($this->next_maintenance_date);
        
        if ($daysUntilMaintenance <= 7) {
            return 'Due Soon';
        } elseif ($daysUntilMaintenance <= 30) {
            return 'Upcoming';
        } else {
            return 'Scheduled';
        }
    }

    // Scopes
    public function scopeByCondition($query, $condition)
    {
        return $query->where('condition', $condition);
    }

    public function scopeWarrantyExpired($query)
    {
        return $query->where('warranty_expiry', '<', now());
    }

    public function scopeWarrantyActive($query)
    {
        return $query->where('warranty_expiry', '>=', now());
    }
} 