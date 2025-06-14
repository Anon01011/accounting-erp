<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AssetMaintenance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'asset_id',
        'maintenance_date',
        'maintenance_type',
        'description',
        'cost',
        'performed_by',
        'next_maintenance_date',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'maintenance_date' => 'datetime',
        'next_maintenance_date' => 'datetime',
        'cost' => 'decimal:2'
    ];

    // Maintenance Types
    const TYPE_PREVENTIVE = 'preventive';
    const TYPE_CORRECTIVE = 'corrective';
    const TYPE_PREDICTIVE = 'predictive';
    const TYPE_CONDITION_BASED = 'condition_based';

    // Constants for maintenance types
    const TYPES = [
        'routine' => 'Routine Maintenance',
        'repair' => 'Repair',
        'inspection' => 'Inspection',
        'upgrade' => 'Upgrade',
        'other' => 'Other',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($maintenance) {
            Log::info('AssetMaintenance creating:', $maintenance->toArray());
        });

        static::created(function ($maintenance) {
            Log::info('AssetMaintenance created:', $maintenance->toArray());
        });

        static::updating(function ($maintenance) {
            Log::info('AssetMaintenance updating (original):', $maintenance->getOriginal());
            Log::info('AssetMaintenance updating (changes):', $maintenance->getDirty());
        });

        static::updated(function ($maintenance) {
            Log::info('AssetMaintenance updated:', $maintenance->toArray());
        });
    }

    // Relationships
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(AssetDocument::class);
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
    public function scopePreventive($query)
    {
        return $query->where('maintenance_type', self::TYPE_PREVENTIVE);
    }

    public function scopeCorrective($query)
    {
        return $query->where('maintenance_type', self::TYPE_CORRECTIVE);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('next_maintenance_date', '>', now())
                    ->where('status', true);
    }

    public function scopeOverdue($query)
    {
        return $query->where('next_maintenance_date', '<', now())
                    ->where('status', true);
    }

    // Methods
    public function createMaintenanceTransaction(): AssetTransaction
    {
        return $this->asset->transactions()->create([
            'type' => AssetTransaction::TYPE_MAINTENANCE,
            'amount' => $this->cost,
            'date' => $this->maintenance_date,
            'description' => $this->description,
            'created_by' => $this->created_by
        ]);
    }

    public function isOverdue(): bool
    {
        return $this->next_maintenance_date && $this->next_maintenance_date->isPast();
    }

    public function getDaysUntilNextMaintenance(): int
    {
        return $this->next_maintenance_date->diffInDays(now());
    }

    public function markAsCompleted(): bool
    {
        $this->status = false;
        return $this->save();
    }

    public function scheduleNextMaintenance(\DateTime $date): bool
    {
        $this->next_maintenance_date = $date;
        $this->status = true;
        return $this->save();
    }
} 