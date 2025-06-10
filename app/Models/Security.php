<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Security extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'two_factor_enabled',
        'two_factor_secret',
        'last_password_change',
        'password_expiry_days',
        'session_timeout_minutes',
        'login_attempts',
        'last_login_attempt',
        'ip_whitelist',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'two_factor_enabled' => 'boolean',
        'last_password_change' => 'datetime',
        'last_login_attempt' => 'datetime',
        'ip_whitelist' => 'array',
        'status' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
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

    public function scopeInactive($query)
    {
        return $query->where('status', false);
    }
} 