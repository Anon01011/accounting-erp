<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetDisposalLog extends Model
{
    protected $fillable = [
        'asset_id',
        'user_id',
        'disposal_date',
        'disposal_value',
        'disposal_reason',
        'status'
    ];

    protected $casts = [
        'disposal_date' => 'date',
        'disposal_value' => 'decimal:2'
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
} 