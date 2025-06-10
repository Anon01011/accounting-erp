<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JournalEntry extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'entry_date',
        'reference_no',
        'description',
        'status',
        'created_by',
        'updated_by',
        'posted_by',
        'posted_at'
    ];

    protected $casts = [
        'entry_date' => 'date',
        'posted_at' => 'datetime'
    ];

    // Relationships
    public function items(): HasMany
    {
        return $this->hasMany(JournalEntryItem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function poster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopePosted($query)
    {
        return $query->where('status', 'posted');
    }

    public function scopeVoid($query)
    {
        return $query->where('status', 'void');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Methods
    public function canBeEdited()
    {
        return $this->status === 'draft';
    }

    public function canBePosted()
    {
        return $this->status === 'draft' && $this->items()->sum('debit') === $this->items()->sum('credit');
    }

    public function canBeVoided()
    {
        return $this->status === 'posted';
    }

    public function post()
    {
        if (!$this->canBePosted()) {
            throw new \Exception('Journal entry cannot be posted.');
        }

        $this->update([
            'status' => 'posted',
            'posted_by' => auth()->id(),
            'posted_at' => now()
        ]);
    }

    public function void()
    {
        if (!$this->canBeVoided()) {
            throw new \Exception('Journal entry cannot be voided.');
        }

        $this->update(['status' => 'void']);
    }

    public function generateReference()
    {
        $prefix = 'JE';
        $date = now()->format('Ymd');
        $lastEntry = static::where('reference_no', 'like', "{$prefix}{$date}%")
            ->orderBy('reference_no', 'desc')
            ->first();

        if ($lastEntry) {
            $sequence = (int) substr($lastEntry->reference_no, -4) + 1;
        } else {
            $sequence = 1;
        }

        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
