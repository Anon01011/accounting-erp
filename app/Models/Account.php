<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @account Base Account Model
 */
class Account extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'type_code',
        'group_code',
        'class_code',
        'account_code',
        'name',
        'description',
        'is_active',
        'parent_id',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the parent account
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Get the child accounts
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * Get all transactions for this account
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'account_id');
    }

    /**
     * Get the account's current balance
     */
    public function getBalanceAttribute()
    {
        return $this->transactions()->sum('amount');
    }

    /**
     * Get the account type
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(AccountType::class, 'type_code', 'code');
    }

    /**
     * Get the account group
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(AccountGroup::class, 'group_code', 'code');
    }

    /**
     * Get the account class
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(AccountClass::class, 'class_code', 'code');
    }

    /**
     * Get the account type name
     */
    public function getTypeNameAttribute(): string
    {
        return config('accounting.account_types.' . $this->type_code, 'Unknown');
    }

    /**
     * Get the account group name
     */
    public function getGroupNameAttribute(): string
    {
        return config('accounting.account_groups.' . $this->type_code . '.' . $this->group_code, 'Unknown');
    }

    /**
     * Get the account class name
     */
    public function getClassNameAttribute(): string
    {
        return config('accounting.account_classes.' . $this->type_code . '.' . $this->group_code . '.' . $this->class_code, 'Unknown');
    }

    /**
     * Scope a query to only include active accounts
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include parent accounts
     */
    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope a query to only include child accounts
     */
    public function scopeChildren($query)
    {
        return $query->whereNotNull('parent_id');
    }

    /**
     * Get all accounts in a tree structure
     */
    public static function getTree()
    {
        return static::with('children')
            ->whereNull('parent_id')
            ->orderBy('type_code')
            ->orderBy('group_code')
            ->orderBy('class_code')
            ->orderBy('account_code')
            ->get();
    }

    /**
     * Get the full account code
     */
    public function getFullAccountCodeAttribute(): string
    {
        return sprintf('%s.%s.%s.%s', $this->type_code, $this->group_code, $this->class_code, $this->account_code);
    }

    /**
     * Get the account's path in the tree
     */
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

    /**
     * Get the account's level in the tree
     */
    public function getLevelAttribute()
    {
        return count($this->path) - 1;
    }

    /**
     * Check if the account can be deleted
     */
    public function canBeDeleted()
    {
        return !$this->children()->exists() && !$this->transactions()->exists();
    }
} 