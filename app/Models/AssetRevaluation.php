<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetRevaluation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'asset_id',
        'revaluation_date',
        'previous_value',
        'new_value',
        'revaluation_reason',
        'approved_by',
        'journal_entry_id',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'revaluation_date' => 'date',
        'previous_value' => 'decimal:2',
        'new_value' => 'decimal:2'
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getRevaluationAmount()
    {
        return $this->new_value - $this->previous_value;
    }
} 