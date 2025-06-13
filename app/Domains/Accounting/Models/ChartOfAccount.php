<?php

namespace App\Domains\Accounting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\JournalEntry;
use App\Models\JournalEntryItem;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

use Illuminate\Support\Facades\DB;

class ChartOfAccount extends Model
{
    use SoftDeletes;

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
        'is_master' => 'boolean',
        'balance' => 'decimal:2',
    ];

    // Account Levels
    const LEVEL_TYPE = 1;
    const LEVEL_GROUP = 2;
    const LEVEL_CLASS = 3;
    const LEVEL_ACCOUNT = 4;

    // Relationships
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_id');
    }

    public function assetDetails(): HasOne
    {
        return $this->hasOne(\App\Models\AssetDetail::class, 'account_id');
    }

    public function journalEntries(): HasManyThrough
    {
        return $this->hasManyThrough(
            JournalEntry::class,
            JournalEntryItem::class,
            'chart_of_account_id', // Foreign key on JournalEntryItem table...
            'id', // Foreign key on JournalEntry table...
            'id', // Local key on ChartOfAccount table...
            'journal_entry_id' // Local key on JournalEntryItem table...
        );
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Accessors
    public function getFullAccountCodeAttribute(): string
    {
        if ($this->is_master) {
            return $this->type_code;
        }
        
        return sprintf('%s.%s.%s.%s', 
            $this->type_code,
            $this->group_code,
            $this->class_code,
            $this->account_code
        );
    }

    public function getTypeNameAttribute(): string
    {
        return config('accounting.account_types.' . $this->type_code, 'Unknown');
    }

    public function getGroupNameAttribute(): string
    {
        return config('accounting.groups.' . $this->type_code . '.' . $this->group_code, 'Unknown');
    }

    public function getClassNameAttribute(): string
    {
        return config('accounting.classes.' . $this->type_code . '.' . $this->group_code . '.' . $this->class_code, 'Unknown');
    }

    public function getFullNameAttribute(): string
    {
        if ($this->is_master) {
            return $this->name;
        }
        return "{$this->full_account_code} - {$this->name}";
    }

    public function getLevelNameAttribute(): string
    {
        return match($this->account_level) {
            self::LEVEL_TYPE => 'Account Type',
            self::LEVEL_GROUP => 'Account Group',
            self::LEVEL_CLASS => 'Account Class',
            self::LEVEL_ACCOUNT => 'Account',
            default => 'Unknown'
        };
    }

    public function getFullPathAttribute(): string
    {
        return $this->account_code . ' - ' . $this->name;
    }

    public function getLevelAttribute(): string
    {
        if (empty($this->type_code)) {
            return 'account';
        }
        if (empty($this->group_code)) {
            return 'type';
        }
        if (empty($this->class_code)) {
            return 'group';
        }
        return 'class';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeMaster($query)
    {
        return $query->where('is_master', true);
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

    public function scopeOfLevel($query, $level)
    {
        return $query->where('account_level', $level);
    }

    public function scopeType($query)
    {
        return $query->where('level', 'type');
    }

    public function scopeGroup($query)
    {
        return $query->where('level', 'group');
    }

    public function scopeClass($query)
    {
        return $query->where('level', 'class');
    }

    public function scopeAccount($query)
    {
        return $query->where('level', 'account');
    }

    // Balance Methods
    public function getBalanceAttribute()
    {
        if ($this->is_master) {
            return $this->calculateMasterBalance();
        }
        return $this->journalEntries()->sum('amount');
    }

    public function getDebitBalanceAttribute()
    {
        if ($this->is_master) {
            return $this->calculateMasterDebitBalance();
        }
        return $this->journalEntries()->where('type', 'debit')->sum('amount');
    }

    public function getCreditBalanceAttribute()
    {
        if ($this->is_master) {
            return $this->calculateMasterCreditBalance();
        }
        return $this->journalEntries()->where('type', 'credit')->sum('amount');
    }

    public function getNetBalanceAttribute()
    {
        return $this->debit_balance - $this->credit_balance;
    }

    // Helper Methods
    public static function generateAccountCode($typeCode, $groupCode, $classCode)
    {
        $lastAccount = static::where('type_code', $typeCode)
            ->where('group_code', $groupCode)
            ->where('class_code', $classCode)
            ->where('is_master', false)
            ->orderBy('account_code', 'desc')
            ->first();

        if (!$lastAccount) {
            return '0001';
        }

        return str_pad((int)$lastAccount->account_code + 1, 4, '0', STR_PAD_LEFT);
    }

    public function canBeDeleted()
    {
        return !$this->children()->exists() && !$this->journalEntries()->exists();
    }

    // Master Account Methods
    protected function calculateMasterBalance()
    {
        return $this->children()
            ->with('children')
            ->get()
            ->sum(function ($account) {
                return $account->is_master ? $account->calculateMasterBalance() : $account->balance;
            });
    }

    protected function calculateMasterDebitBalance()
    {
        return $this->children()
            ->with('children')
            ->get()
            ->sum(function ($account) {
                return $account->is_master ? $account->calculateMasterDebitBalance() : $account->debit_balance;
            });
    }

    protected function calculateMasterCreditBalance()
    {
        return $this->children()
            ->with('children')
            ->get()
            ->sum(function ($account) {
                return $account->is_master ? $account->calculateMasterCreditBalance() : $account->credit_balance;
            });
    }

    // Static Methods for Master Account Creation
    public static function createMasterAccount($typeCode, $name, $description = null)
    {
        return static::create([
            'type_code' => $typeCode,
            'name' => $name,
            'description' => $description,
            'is_master' => true,
            'account_level' => self::LEVEL_TYPE,
            'is_active' => true
        ]);
    }

    public static function createMasterGroup($typeCode, $groupCode, $name, $description = null)
    {
        $typeAccount = static::where('type_code', $typeCode)
            ->where('is_master', true)
            ->first();

        if (!$typeAccount) {
            throw new \Exception("Master account type not found: {$typeCode}");
        }

        return static::create([
            'type_code' => $typeCode,
            'group_code' => $groupCode,
            'name' => $name,
            'description' => $description,
            'parent_id' => $typeAccount->id,
            'is_master' => true,
            'account_level' => self::LEVEL_GROUP,
            'is_active' => true
        ]);
    }

    public static function createMasterClass($typeCode, $groupCode, $classCode, $name, $description = null)
    {
        $groupAccount = static::where('type_code', $typeCode)
            ->where('group_code', $groupCode)
            ->where('is_master', true)
            ->first();

        if (!$groupAccount) {
            throw new \Exception("Master account group not found: {$typeCode}.{$groupCode}");
        }

        return static::create([
            'type_code' => $typeCode,
            'group_code' => $groupCode,
            'class_code' => $classCode,
            'name' => $name,
            'description' => $description,
            'parent_id' => $groupAccount->id,
            'is_master' => true,
            'account_level' => self::LEVEL_CLASS,
            'is_active' => true
        ]);
    }

    public function updateBalance(): void
    {
        $this->balance = $this->journalEntries()
            ->where('is_debit', true)
            ->sum('amount') - $this->journalEntries()
            ->where('is_debit', false)
            ->sum('amount');
        
        $this->save();
    }

    public function getChildrenRecursively(): array
    {
        $children = [];
        foreach ($this->children as $child) {
            $children[] = $child;
            $children = array_merge($children, $child->getChildrenRecursively());
        }
        return $children;
    }

    public function getParentChain(): array
    {
        $chain = [];
        $current = $this;
        while ($current->parent) {
            array_unshift($chain, $current->parent);
            $current = $current->parent;
        }
        return $chain;
    }

    public function isDescendantOf(ChartOfAccount $account): bool
    {
        return in_array($account->id, array_map(fn($parent) => $parent->id, $this->getParentChain()));
    }

    public function isAncestorOf(ChartOfAccount $account): bool
    {
        return $account->isDescendantOf($this);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($account) {
            if ($account->parent_id) {
                $parent = static::find($account->parent_id);
                if ($parent) {
                    $account->type_code = $parent->type_code;
                    $account->group_code = $parent->group_code;
                    $account->class_code = $parent->class_code;
                }
            }
        });

        static::updating(function ($account) {
            if ($account->isDirty('parent_id')) {
                $parent = static::find($account->parent_id);
                if ($parent) {
                    $account->type_code = $parent->type_code;
                    $account->group_code = $parent->group_code;
                    $account->class_code = $parent->class_code;
                }
            }
        });
    }
} 