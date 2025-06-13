<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetCategory extends Model
{
    use SoftDeletes;

    protected $table = 'asset_categories';
    protected $fillable = [
        'code',
        'name',
        'description',
        'depreciation_method',
        'default_depreciation_rate',
        'default_useful_life',
        'is_active',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'default_depreciation_rate' => 'decimal:2',
        'default_useful_life' => 'integer',
        'is_active' => 'boolean'
    ];

    // Relationships
    public function assets(): HasMany
    {
        return $this->hasMany(ChartOfAccount::class, 'group_code', 'code');
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
        return $query->where('is_active', true);
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
} 