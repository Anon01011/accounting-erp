<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AssetDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'asset_id',
        'name',
        'type',
        'category',
        'file_path',
        'file_size',
        'mime_type',
        'version',
        'description',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'file_size' => 'integer',
        'version' => 'integer'
    ];

    // Document Categories
    const CATEGORY_WARRANTY = 'warranty';
    const CATEGORY_MAINTENANCE = 'maintenance';
    const CATEGORY_INVOICE = 'invoice';
    const CATEGORY_MANUAL = 'manual';
    const CATEGORY_CERTIFICATE = 'certificate';
    const CATEGORY_OTHER = 'other';

    // Allowed File Types
    const ALLOWED_MIME_TYPES = [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    ];

    // Maximum File Size (10MB)
    const MAX_FILE_SIZE = 10485760;

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function documentable(): MorphTo
    {
        return $this->morphTo();
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
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeLatestVersion($query)
    {
        return $query->orderBy('version', 'desc');
    }

    // Methods
    public function getFileSizeInMB(): float
    {
        return round($this->file_size / 1048576, 2);
    }

    public function getFileExtension(): string
    {
        return pathinfo($this->file_path, PATHINFO_EXTENSION);
    }

    public function isImage(): bool
    {
        return in_array($this->mime_type, ['image/jpeg', 'image/png']);
    }

    public function isPDF(): bool
    {
        return $this->mime_type === 'application/pdf';
    }

    public function incrementVersion(): void
    {
        $this->version++;
        $this->save();
    }

    public static function getCategories(): array
    {
        return [
            self::CATEGORY_WARRANTY => 'Warranty',
            self::CATEGORY_MAINTENANCE => 'Maintenance',
            self::CATEGORY_INVOICE => 'Invoice',
            self::CATEGORY_MANUAL => 'Manual',
            self::CATEGORY_CERTIFICATE => 'Certificate',
            self::CATEGORY_OTHER => 'Other'
        ];
    }
}
