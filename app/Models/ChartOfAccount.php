<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChartOfAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type_code',
        'group_code',
        'class_code',
        'account_code',
        'name',
        'description',
        'parent_id',
        'is_active',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function type()
    {
        return $this->belongsTo(AccountType::class, 'type_code', 'code');
    }

    public function group()
    {
        return $this->belongsTo(AccountGroup::class, 'group_code', 'code');
    }

    public function class()
    {
        return $this->belongsTo(AccountClass::class, 'class_code', 'code');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(JournalEntryItem::class, 'chart_of_account_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getTypeNameAttribute(): string
    {
        return config('accounting.account_types.' . $this->type_code, 'Unknown');
    }

    public function getGroupNameAttribute(): string
    {
        return config('accounting.account_groups.' . $this->type_code . '.' . $this->group_code, 'Unknown');
    }

    public function getClassNameAttribute(): string
    {
        return config('accounting.account_classes.' . $this->type_code . '.' . $this->group_code . '.' . $this->class_code, 'Unknown');
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->account_code} - {$this->name}";
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, $typeCode)
    {
        return $query->where('type_code', $typeCode);
    }

    public function scopeOfGroup($query, $groupCode)
    {
        return $query->where('group_code', $groupCode);
    }

    public function scopeOfClass($query, $classCode)
    {
        return $query->where('class_code', $classCode);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeChildren($query)
    {
        return $query->whereNotNull('parent_id');
    }

    public function getFullAccountCodeAttribute()
    {
        return sprintf('%s.%s.%s.%s', 
            $this->type_code,
            $this->group_code,
            $this->class_code,
            $this->account_code
        );
    }

    public function getPathAttribute()
    {
        $path = [$this];
        $current = $this;

        while ($current->parent) {
            $current = $current->parent;
            array_unshift($path, $current);
        }

        return $path;
    }

    public function getLevelAttribute()
    {
        return count($this->path) - 1;
    }

    public static function generateAccountCode($typeCode, $groupCode, $classCode)
    {
        $lastAccount = static::where('type_code', $typeCode)
            ->where('group_code', $groupCode)
            ->where('class_code', $classCode)
            ->orderBy('account_code', 'desc')
            ->first();

        if (!$lastAccount) {
            return '0001';
        }

        return str_pad((int)$lastAccount->account_code + 1, 4, '0', STR_PAD_LEFT);
    }

    public function canBeDeleted()
    {
        return !$this->children()->exists() && !$this->transactions()->exists();
    }

    public function getBalanceAttribute()
    {
        return $this->transactions()->sum(DB::raw('debit - credit'));
    }

    public function getDebitBalanceAttribute()
    {
        return $this->transactions()->sum('debit');
    }

    public function getCreditBalanceAttribute()
    {
        return $this->transactions()->sum('credit');
    }

    public function getNetBalanceAttribute()
    {
        return $this->debit_balance - $this->credit_balance;
    }

    public static function getAssetCategories()
    {
        return config('accounting.asset_categories');
    }
}
