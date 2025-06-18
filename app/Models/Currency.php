<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Currency extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'symbol',
        'exchange_rate',
        'status',
        'notes'
    ];

    protected $casts = [
        'exchange_rate' => 'decimal:6',
        'status' => 'boolean'
    ];
}
