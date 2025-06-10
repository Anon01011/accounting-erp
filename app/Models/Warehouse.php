<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'address',
        'manager',
        'phone',
        'email',
        'status',
        'notes'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function transfers()
    {
        return $this->hasMany(Transfer::class, 'source_warehouse_id');
    }

    public function receivedTransfers()
    {
        return $this->hasMany(Transfer::class, 'destination_warehouse_id');
    }

    public function counts()
    {
        return $this->hasMany(Count::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', false);
    }
} 