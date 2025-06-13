<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaxGroup extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
        'is_default',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    /**
     * Get the tax rates for the tax group.
     */
    public function taxRates(): HasMany
    {
        return $this->hasMany(TaxRate::class);
    }

    /**
     * Get the active tax rates for the tax group.
     */
    public function activeTaxRates()
    {
        return $this->taxRates()->where('is_active', true);
    }

    /**
     * Get the default tax rate for the tax group.
     */
    public function defaultTaxRate()
    {
        return $this->taxRates()
            ->where('is_active', true)
            ->where('is_default', true)
            ->where('effective_from', '<=', now())
            ->where(function ($query) {
                $query->whereNull('effective_to')
                    ->orWhere('effective_to', '>=', now());
            })
            ->first();
    }

    public function getActiveRates()
    {
        return $this->taxRates()
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('effective_from')
                    ->orWhere('effective_from', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('effective_to')
                    ->orWhere('effective_to', '>=', now());
            })
            ->get();
    }

    public function getDefaultRate()
    {
        return $this->taxRates()
            ->where('is_active', true)
            ->where('is_default', true)
            ->where(function ($query) {
                $query->whereNull('effective_from')
                    ->orWhere('effective_from', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('effective_to')
                    ->orWhere('effective_to', '>=', now());
            })
            ->first();
    }

    protected static function boot()
    {
        parent::boot();

        // Auto-generate code when creating
        static::creating(function ($taxGroup) {
            $taxGroup->code = 'GRP-' . str_pad((static::count() + 1), 3, '0', STR_PAD_LEFT);
        });

        // Handle default setting
        static::saving(function ($taxGroup) {
            if ($taxGroup->is_default) {
                // If this group is being set as default, unset any other default
                static::where('id', '!=', $taxGroup->id)
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            }
        });
    }
}