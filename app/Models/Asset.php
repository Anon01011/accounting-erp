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
        'chart_of_account_id',
        'name',
        'code',
        'category_id',
        'description',
        'purchase_date',
        'purchase_price',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_price' => 'decimal:2',
        'status' => 'boolean',
    ];

    // Relationships
    public function chartOfAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class, 'category_id');
    }

    public function details(): HasOne
    {
        return $this->hasOne(AssetDetail::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(AssetTransaction::class);
    }

    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(AssetMaintenance::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(AssetDocument::class);
    }

    public function journalEntries(): HasManyThrough
    {
        return $this->hasManyThrough(
            JournalEntry::class,
            AssetTransaction::class,
            'asset_id', // Foreign key on AssetTransaction table
            'id', // Foreign key on JournalEntry table
            'id', // Local key on Asset table
            'reference_id' // Local key on AssetTransaction table
        )->where('asset_transactions.reference_type', 'journal_entry');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
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
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByLocation($query, $warehouseId)
    {
        return $query->where('warehouse_id', $warehouseId);
    }

    // Methods
    public function getCurrentValue(): float
    {
        return $this->purchase_price - $this->getAccumulatedDepreciation();
    }

    public function getAccumulatedDepreciation(): float
    {
        return $this->transactions()
            ->where('type', 'depreciation')
            ->sum('amount');
    }

    public function getDepreciationAmount(): float
    {
        if (!$this->details) {
            return 0;
        }

        $method = $this->details->depreciation_method;
        $rate = $this->details->depreciation_rate;
        $life = $this->details->useful_life;
        $cost = $this->purchase_price;

        switch ($method) {
            case 'straight_line':
                return $cost / $life;
            case 'declining_balance':
                return $cost * ($rate / 100);
            case 'sum_of_years':
                $sum = ($life * ($life + 1)) / 2;
                $remainingLife = $life - $this->getAge();
                return ($cost * $remainingLife) / $sum;
            default:
                return 0;
        }
    }

    public function getAge(): int
    {
        return $this->purchase_date->diffInYears(now());
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
} 