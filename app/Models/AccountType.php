<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function groups()
    {
        return $this->hasMany(AccountGroup::class, 'type_code', 'code');
    }

    public function accounts()
    {
        return $this->hasManyThrough(
            ChartOfAccount::class,
            AccountGroup::class,
            'type_code',
            'group_code',
            'code',
            'code'
        );
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
} 