<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Localization extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'language',
        'locale',
        'date_format',
        'time_format',
        'timezone',
        'currency',
        'currency_symbol',
        'currency_position',
        'thousand_separator',
        'decimal_separator',
        'decimal_places',
        'status',
        'is_default',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'status' => 'boolean',
        'is_default' => 'boolean',
        'decimal_places' => 'integer'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', false);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
} 