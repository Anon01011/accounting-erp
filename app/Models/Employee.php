<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'department_id',
        'position_id',
        'hire_date',
        'salary',
        'emergency_contact_name',
        'emergency_contact_phone',
        'photo',
        'notes',
        'is_active',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'hire_date' => 'date',
        'salary' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    // Relationships
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
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
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getFullAddressAttribute(): string
    {
        return "{$this->address}, {$this->city}, {$this->state} {$this->postal_code}, {$this->country}";
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeByPosition($query, $positionId)
    {
        return $query->where('position_id', $positionId);
    }

    // Methods
    public function getCurrentAttendance()
    {
        return $this->attendances()
            ->whereDate('date', today())
            ->first();
    }

    public function getMonthlyAttendance($month = null)
    {
        $month = $month ?? now();
        return $this->attendances()
            ->whereMonth('date', $month->month)
            ->whereYear('date', $month->year)
            ->get();
    }

    public function getCurrentPayroll()
    {
        return $this->payrolls()
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->first();
    }

    public function calculateMonthlySalary()
    {
        $attendance = $this->getMonthlyAttendance();
        $workingDays = $attendance->where('status', 'present')->count();
        $totalDays = now()->daysInMonth;
        
        return ($this->salary / $totalDays) * $workingDays;
    }
} 