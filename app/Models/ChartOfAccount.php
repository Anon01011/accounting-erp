<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\AssetCalculations;

class ChartOfAccount extends Model
{
    use HasFactory, SoftDeletes, AssetCalculations;

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

    // Constants for ID structure
    const COMPANY_CODE = '2184';
    const ID_LENGTH = 19;
    const TYPE_CODE_LENGTH = 3;
    const GROUP_CODE_LENGTH = 8;
    const ACCOUNT_SEQUENCE_LENGTH = 4;

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

    /**
     * Generate a new account ID
     */
    public static function generateAccountId($typeCode, $groupCode, $classCode)
    {
        // Get the last account for this type/group/class
        $lastAccount = static::where('type_code', $typeCode)
            ->where('group_code', $groupCode)
            ->where('class_code', $classCode)
            ->orderBy('account_code', 'desc')
            ->first();

        // Extract sequence number
        $sequence = 1;
        if ($lastAccount) {
            $sequence = (int)substr($lastAccount->account_code, -4) + 1;
        }

        // Format: COMPANY_CODE + TYPE_CODE + GROUP_CODE + SEQUENCE
        return sprintf(
            '%s%s%s%04d',
            self::COMPANY_CODE,
            str_pad($typeCode, self::TYPE_CODE_LENGTH, '0', STR_PAD_LEFT),
            str_pad($groupCode, self::GROUP_CODE_LENGTH, '0', STR_PAD_LEFT),
            $sequence
        );
    }

    /**
     * Get the account type code from the full ID
     */
    public function getTypeCodeFromId()
    {
        return (int)substr($this->account_code, 4, self::TYPE_CODE_LENGTH);
    }

    /**
     * Get the account group code from the full ID
     */
    public function getGroupCodeFromId()
    {
        return (int)substr($this->account_code, 7, self::GROUP_CODE_LENGTH);
    }

    /**
     * Get the account sequence from the full ID
     */
    public function getSequenceFromId()
    {
        return (int)substr($this->account_code, -4);
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

    // Asset specific relationships
    public function assetDetails(): HasMany
    {
        return $this->hasMany(AssetDetail::class, 'account_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(AssetDocument::class, 'asset_id');
    }

    public function assetTransactions(): HasMany
    {
        return $this->hasMany(AssetTransaction::class, 'asset_id');
    }

    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(AssetMaintenance::class, 'asset_id');
    }

    // Scopes
    public function scopeAssets($query)
    {
        return $query->where('type_code', '01');
    }

    public function scopeByCategory($query, $categoryCode)
    {
        return $query->where('group_code', $categoryCode);
    }

    // Accessors
    public function getCategoryNameAttribute(): string
    {
        return config('accounting.asset_categories.' . $this->group_code, 'Unknown Category');
    }

    public function getIsAssetAttribute(): bool
    {
        return $this->type_code === '01';
    }

    // Methods
    public function updateStatus(bool $status): bool
    {
        $this->is_active = $status;
        $saved = $this->save();

        if ($saved && $status) {
            // If activating, also activate all children
            $this->children()->update(['is_active' => true]);
        }

        return $saved;
    }

    public function getBalance(): float
    {
        return $this->assetTransactions()
            ->where('is_active', true)
            ->sum('amount');
    }

    public function getDepreciationAmount(): float
    {
        return $this->assetTransactions()
            ->where('is_active', true)
            ->where('transaction_type', 'depreciation')
            ->sum('amount');
    }

    public function getNetBookValue(): float
    {
        return $this->getBalance() - $this->getDepreciationAmount();
    }
}
