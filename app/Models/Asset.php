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
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Traits\AssetCalculations;

class Asset extends Model
{
    use HasFactory, SoftDeletes, AssetCalculations;

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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($asset) {
            Log::info('Asset creating:', $asset->toArray());
        });

        static::created(function ($asset) {
            Log::info('Asset created:', $asset->toArray());
        });

        static::updating(function ($asset) {
            Log::info('Asset updating (original):', $asset->getOriginal());
            Log::info('Asset updating (changes):', $asset->getDirty());
        });

        static::updated(function ($asset) {
            Log::info('Asset updated:', $asset->toArray());
        });
    }

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

    public function disposal_logs()
    {
        return $this->hasMany(AssetDisposalLog::class);
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
        $assetDetail = $this->details->first();
        if (!$assetDetail) {
            return 0;
        }

        // Get the last depreciation date
        $lastDepreciation = $this->transactions()
            ->where('type', 'depreciation')
            ->latest()
            ->first();

        // If no previous depreciation, use purchase date
        $startDate = $lastDepreciation ? $lastDepreciation->date : $assetDetail->purchase_date;
        
        // Calculate months since last depreciation or purchase
        $monthsSinceLastDepreciation = now()->diffInMonths($startDate);
        
        // If less than a month has passed, return 0
        if ($monthsSinceLastDepreciation < 1) {
            return 0;
        }

        // Get depreciation parameters from asset detail
        $depreciationMethod = $assetDetail->depreciation_method ?? 'straight_line';
        $depreciationRate = $assetDetail->depreciation_rate ?? 0;
        $usefulLife = $assetDetail->useful_life ?? 0;
        $salvageValue = $assetDetail->residual_value ?? 0;

        // Calculate depreciation based on method
        switch ($depreciationMethod) {
            case 'straight_line':
                $annualDepreciation = ($assetDetail->purchase_price - $salvageValue) / $usefulLife;
                $monthlyDepreciation = $annualDepreciation / 12;
                return $monthlyDepreciation * $monthsSinceLastDepreciation;

            case 'declining_balance':
                $bookValue = $this->getCurrentValue();
                $annualDepreciation = $bookValue * ($depreciationRate / 100);
                $monthlyDepreciation = $annualDepreciation / 12;
                return $monthlyDepreciation * $monthsSinceLastDepreciation;

            case 'sum_of_years':
                $remainingLife = $usefulLife - $this->getAge();
                if ($remainingLife <= 0) return 0;
                
                $sumOfYears = ($usefulLife * ($usefulLife + 1)) / 2;
                $depreciationFactor = $remainingLife / $sumOfYears;
                $annualDepreciation = ($assetDetail->purchase_price - $salvageValue) * $depreciationFactor;
                $monthlyDepreciation = $annualDepreciation / 12;
                return $monthlyDepreciation * $monthsSinceLastDepreciation;

            case 'units_of_production':
                // This method requires additional tracking of units produced
                // For now, return 0 as it needs to be implemented based on actual usage
                return 0;

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
        $detail = $this->details->first();
        return $detail && 
               $detail->warranty_expiry && 
               Carbon::parse($detail->warranty_expiry)->isFuture();
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

    public function getCurrentBookValue()
    {
        return $this->current_value ?? $this->purchase_price;
    }

    public function getNextDepreciationDate()
    {
        if (!$this->purchase_date || !$this->depreciation_rate) {
            return now();
        }

        $lastDepreciationDate = $this->purchase_date->copy()->addMonths(
            floor(now()->diffInMonths($this->purchase_date))
        );

        return $lastDepreciationDate->addMonth();
    }
} 