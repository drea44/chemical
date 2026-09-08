<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditTrailController extends Controller
{
    public function index(Request $request)
    {
        // Only ADMIN and AUDITOR
        if (!in_array(auth()->user()->role, ['ADMIN', 'AUDITOR'])) {
            abort(403);
        }

        $query = AuditLog::with('user');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                  ->orWhere('module', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        if ($userId = $request->get('user')) {
            $query->where('user_id', $userId);
        }

        if ($action = $request->get('action')) {
            $query->where('action', $action);
        }

        if ($module = $request->get('module')) {
            $query->where('module', $module);
        }

        if ($dateFrom = $request->get('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->get('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $logs    = $query->orderBy('created_at', 'desc')->paginate(30)->withQueryString();
        $users   = User::orderBy('name')->get(['id', 'name']);
        $actions = AuditLog::distinct()->pluck('action')->sort()->values();
        $modules = AuditLog::distinct()->pluck('module')->sort()->values();

        return view('audit-trail.index', compact('logs', 'users', 'actions', 'modules'));
    }
}
