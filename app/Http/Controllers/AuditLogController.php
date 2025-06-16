<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')
            ->when($request->model_type, function ($q) use ($request) {
                return $q->where('model_type', $request->model_type);
            })
            ->when($request->model_id, function ($q) use ($request) {
                return $q->where('model_id', $request->model_id);
            })
            ->when($request->action, function ($q) use ($request) {
                return $q->where('action', $request->action);
            })
            ->when($request->user_id, function ($q) use ($request) {
                return $q->where('user_id', $request->user_id);
            })
            ->when($request->date_from, function ($q) use ($request) {
                return $q->whereDate('created_at', '>=', $request->date_from);
            })
            ->when($request->date_to, function ($q) use ($request) {
                return $q->whereDate('created_at', '<=', $request->date_to);
            });

        $auditLogs = $query->latest()->paginate(20);

        return view('audit-logs.index', compact('auditLogs'));
    }

    public function show(AuditLog $auditLog)
    {
        $auditLog->load('user');
        return view('audit-logs.show', compact('auditLog'));
    }
} 