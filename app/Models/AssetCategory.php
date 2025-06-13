<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'asset_categories';
    protected $fillable = [
        'code',
        'name',
        'description',
        'depreciation_method',
        'default_depreciation_rate',
        'default_useful_life',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'default_depreciation_rate' => 'decimal:2',
        'default_useful_life' => 'integer',
        'status' => 'boolean'
    ];

    // Relationships
    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'category_id');
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

    public function scopeInactive($query)
    {
        return $query->where('status', false);
    }

    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }

    // Methods
    public function getTotalAssets(): int
    {
        return $this->assets()->count();
    }

    public function getTotalValue(): float
    {
        return $this->assets()->sum('purchase_price');
    }

    public function getDepreciatedValue(): float
    {
        return $this->assets()->sum('accumulated_depreciation');
    }

    public function getNetBookValue(): float
    {
        return $this->getTotalValue() - $this->getDepreciatedValue();
    }

    public function getDepreciationMethods()
    {
        return [
            'straight_line' => 'Straight Line',
            'declining_balance' => 'Declining Balance',
            'sum_of_years' => 'Sum of Years Digits',
            'units_of_production' => 'Units of Production'
        ];
    }

    public function calculateDepreciation($purchasePrice, $salvageValue, $monthsSincePurchase)
    {
        if ($this->depreciation_method === 'straight_line') {
            $annualDepreciation = ($purchasePrice - $salvageValue) / $this->default_useful_life;
            return min($annualDepreciation * ($monthsSincePurchase / 12), $purchasePrice - $salvageValue);
        }

        return 0;
    }
} 