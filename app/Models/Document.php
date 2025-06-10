<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'title',
        'type',
        'file_path',
        'description',
        'upload_date',
        'expiry_date',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'upload_date' => 'date',
        'expiry_date' => 'date',
        'status' => 'boolean'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

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

    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now());
    }
}