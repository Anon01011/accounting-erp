<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountGroup extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'type_code',
        'code',
        'name',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function type()
    {
        return $this->belongsTo(AccountType::class, 'type_code', 'code');
    }

    public function classes()
    {
        return $this->hasMany(AccountClass::class, 'group_code', 'code')
            ->where('type_code', $this->type_code);
    }

    public function accounts()
    {
        return $this->hasMany(ChartOfAccount::class, 'group_code', 'code');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, $typeCode)
    {
        return $query->where('type_code', $typeCode);
    }
} 