<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait HasAuditLogs
{
    protected static function bootHasAuditLogs()
    {
        static::created(function ($model) {
            $model->logAudit('create', null, $model->getAttributes());
        });

        static::updated(function ($model) {
            $model->logAudit('update', $model->getOriginal(), $model->getChanges());
        });

        static::deleted(function ($model) {
            $model->logAudit('delete', $model->getOriginal(), null);
        });
    }

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    protected function logAudit($action, $oldValues, $newValues)
    {
        if (!Auth::check()) {
            return;
        }

        $this->auditLogs()->create([
            'user_id' => Auth::id(),
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
} 