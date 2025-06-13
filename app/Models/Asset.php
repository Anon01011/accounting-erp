<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Asset extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'category_id',
        'chart_of_account_id',
        'description',
        'purchase_date',
        'purchase_price',
        'current_value',
        'location',
        'status',
        'supplier_id',
        'tax_group_id',
        'warranty_expiry',
        'depreciation_method',
        'depreciation_rate',
        'useful_life',
        'notes',
        'is_active'
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiry' => 'date',
        'purchase_price' => 'decimal:2',
        'current_value' => 'decimal:2',
        'depreciation_rate' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(AssetCategory::class, 'category_id');
    }

    public function chartOfAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'chart_of_account_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function details()
    {
        return $this->hasMany(AssetDetail::class);
    }

    public function maintenanceRecords()
    {
        return $this->hasMany(AssetMaintenance::class);
    }

    public function depreciationRecords(): HasMany
    {
        return $this->hasMany(AssetDepreciation::class);
    }

    public function documents()
    {
        return $this->hasMany(AssetDocument::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function transactions()
    {
        return $this->hasMany(AssetTransaction::class);
    }

    public function journalEntries(): HasManyThrough
    {
        return $this->hasManyThrough(
            JournalEntry::class,
            AssetTransaction::class,
            'asset_id',
            'id',
            'id',
            'reference_id'
        )->where('asset_transactions.reference_type', 'journal_entry');
    }

    public function revaluations()
    {
        return $this->hasMany(AssetRevaluation::class);
    }

    public function impairments()
    {
        return $this->hasMany(AssetImpairment::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function taxGroup(): BelongsTo
    {
        return $this->belongsTo(TaxGroup::class, 'tax_group_id');
    }

    // Accessors
    public function getDepreciationMethodAttribute()
    {
        return $this->details->first()?->depreciation_method;
    }

    public function getDepreciationRateAttribute()
    {
        return $this->details->first()?->depreciation_rate;
    }

    public function getUsefulLifeAttribute()
    {
        return $this->details->first()?->useful_life;
    }

    public function getResidualValueAttribute()
    {
        return $this->details->first()?->residual_value;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', false);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByWarehouse($query, $warehouseId)
    {
        return $query->where('warehouse_id', $warehouseId);
    }

    // Methods
    public function calculateDepreciation()
    {
        if (!$this->purchase_date || !$this->purchase_price) {
            return 0;
        }

        $depreciationMethod = $this->category->depreciation_method ?? 'straight_line';
        $depreciationRate = $this->category->default_depreciation_rate ?? 0;
        $usefulLife = $this->category->default_useful_life ?? 0;

        if ($depreciationMethod === 'straight_line') {
            $annualDepreciation = ($this->purchase_price - $this->salvage_value) / $usefulLife;
            $monthsSincePurchase = now()->diffInMonths($this->purchase_date);
            return min($annualDepreciation * ($monthsSincePurchase / 12), $this->purchase_price - $this->salvage_value);
        }

        return 0;
    }

    public function getCurrentValue()
    {
        return $this->purchase_price - $this->accumulated_depreciation;
    }

    public function getMaintenanceStatus()
    {
        $lastMaintenance = $this->maintenanceRecords()->latest()->first();
        if (!$lastMaintenance) {
            return 'No maintenance records';
        }

        $monthsSinceLastMaintenance = now()->diffInMonths($lastMaintenance->maintenance_date);
        if ($monthsSinceLastMaintenance > 12) {
            return 'Maintenance overdue';
        }

        return 'Maintained';
    }

    public function getAccumulatedDepreciation(): float
    {
        return $this->transactions()
            ->where('type', 'depreciation')
            ->sum('amount');
    }

    public function getDepreciationAmount(): float
    {
        if (!$this->category) {
            return 0;
        }

        $method = $this->category->depreciation_method ?? 'straight_line';
        $rate = floatval($this->category->default_depreciation_rate ?? 0);
        $life = intval($this->category->default_useful_life ?? 0);
        $cost = floatval($this->purchase_price ?? 0);
        $currentValue = floatval($this->getCurrentValue() ?? 0);

        // Return 0 if any required value is invalid
        if ($cost <= 0 || $currentValue <= 0) {
            return 0;
        }

        switch ($method) {
            case 'straight_line':
                if ($life <= 0) {
                    return 0;
                }
                return $cost / $life;

            case 'declining_balance':
                if ($rate <= 0) {
                    return 0;
                }
                return $currentValue * ($rate / 100);

            case 'sum_of_years':
                if ($life <= 0) {
                    return 0;
                }
                $sum = ($life * ($life + 1)) / 2;
                $remainingLife = $life - $this->getAge();
                if ($remainingLife <= 0 || $sum <= 0) {
                    return 0;
                }
                return ($cost * $remainingLife) / $sum;

            case 'double_declining':
                if ($life <= 0) {
                    return 0;
                }
                $rate = (2 / $life) * 100;
                return $currentValue * ($rate / 100);

            case 'units_of_production':
                $totalUnits = floatval($this->category->total_units ?? 0);
                if ($totalUnits <= 0) {
                    return 0;
                }
                $depreciationPerUnit = $cost / $totalUnits;
                $unitsThisYear = $this->getUnitsThisYear();
                return $depreciationPerUnit * $unitsThisYear;

            default:
                return 0;
        }
    }

    public function getAge(): int
    {
        return $this->purchase_date->diffInYears(now());
    }

    public function getUnitsThisYear(): int
    {
        $startOfYear = now()->startOfYear();
        $endOfYear = now()->endOfYear();
        
        return $this->transactions()
            ->where('type', 'production')
            ->whereBetween('date', [$startOfYear, $endOfYear])
            ->sum('units');
    }

    public function isUnderWarranty(): bool
    {
        return $this->details && 
               $this->details->warranty_expiry && 
               $this->details->warranty_expiry->isFuture();
    }

    public function needsMaintenance(): bool
    {
        $lastMaintenance = $this->maintenanceRecords()
            ->latest()
            ->first();

        if (!$lastMaintenance) {
            return true;
        }

        return $lastMaintenance->next_maintenance_date->isPast();
    }

    public function updateStatus(bool $status): bool
    {
        $this->status = $status;
        return $this->save();
    }

    public function getNetBookValue(): float
    {
        return $this->current_value - $this->accumulated_depreciation;
    }

    public function getTaxNetBookValue(): float
    {
        return $this->tax_current_value - $this->tax_accumulated_depreciation;
    }

    public function getDepreciationStatus(): string
    {
        $percentage = ($this->getAccumulatedDepreciation() / $this->purchase_price) * 100;
        
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

    public function getLastRevaluation()
    {
        return $this->revaluations()->latest()->first();
    }

    public function getLastImpairment()
    {
        return $this->impairments()->latest()->first();
    }

    public function isDisposed()
    {
        return !is_null($this->disposal_date);
    }

    public function getDisposalGainLoss()
    {
        if (!$this->isDisposed()) {
            return 0;
        }
        return $this->disposal_value - $this->getNetBookValue();
    }
} 