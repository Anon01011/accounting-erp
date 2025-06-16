<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with(['user', 'auditable']);

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by event type
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by model type
        if ($request->filled('auditable_type')) {
            $query->where('auditable_type', $request->auditable_type);
        }

        // Search in old and new values
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('old_values', 'like', "%{$search}%")
                  ->orWhere('new_values', 'like', "%{$search}%");
            });
        }

        $auditLogs = $query->latest()->paginate(20);

        return view('audit-logs.index', compact('auditLogs'));
    }

    public function show(AuditLog $auditLog)
    {
        $auditLog->load(['user', 'auditable']);
        return view('audit-logs.show', compact('auditLog'));
    }
} 