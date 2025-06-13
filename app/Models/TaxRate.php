<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxRate extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tax_group_id',
        'name',
        'rate',
        'type',
        'effective_from',
        'effective_to',
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
        'rate' => 'decimal:2',
        'effective_from' => 'datetime',
        'effective_to' => 'datetime',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    /**
     * Get the tax group that owns the tax rate.
     */
    public function taxGroup()
    {
        return $this->belongsTo(TaxGroup::class);
    }

    /**
     * Calculate the tax amount for a given amount.
     */
    public function calculateTax($amount)
    {
        if ($this->type === 'percentage') {
            return $amount * ($this->rate / 100);
        }

        return $this->rate;
    }

    /**
     * Check if the tax rate is currently effective.
     */
    public function isEffective()
    {
        $now = now();
        return $this->is_active &&
            $this->effective_from <= $now &&
            ($this->effective_to === null || $this->effective_to >= $now);
    }
}