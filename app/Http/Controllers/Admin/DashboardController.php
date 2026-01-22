<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'total_hotels' => Hotel::count(),
            'total_owners' => User::where('role', 'owner')->count(),
            'total_cleaners' => User::where('role', 'cleaner')->count(),
            'pending_approvals' => User::where('role', 'owner')->where('status', 'pending')->count(),
        ];

        $recentHotels = Hotel::with('owner')
            ->latest()
            ->take(5)
            ->get();

        $owners = User::where('role', 'owner')
            ->withCount('hotels')
            ->latest()
            ->get();

        // Audit log filtering
        $query = Activity::with(['causer', 'subject'])
            ->orderBy('created_at', 'desc');

        // Filter by causer (user who performed the action)
        if ($request->filled('user_id')) {
            if ($request->user_id === 'system') {
                $query->whereNull('causer_id');
            } else {
                $query->where('causer_id', $request->user_id);
            }
        }

        // Filter by event type
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Search in description
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        // Get per_page from request or cookie, default to 50
        $perPage = $request->input('per_page', $request->cookie('audit_per_page', 50));
        $perPage = in_array($perPage, [25, 50, 100, 200]) ? $perPage : 50;

        $auditLogs = $query->paginate($perPage);

        // Get unique causers for filter dropdown
        $causers = User::whereIn('id', Activity::distinct()->pluck('causer_id'))
            ->get(['id', 'name', 'email']);

        return view('admin.dashboard-accordion', compact('stats', 'recentHotels', 'owners', 'auditLogs', 'causers'));
    }

    /**
     * Get recent audit logs as JSON for AJAX updates.
     */
    public function getAuditLogs(Request $request): \Illuminate\Http\JsonResponse
    {
        $query = Activity::with(['causer', 'subject'])
            ->orderBy('created_at', 'desc');

        // Apply same filters as index method
        if ($request->filled('user_id')) {
            if ($request->user_id === 'system') {
                $query->whereNull('causer_id');
            } else {
                $query->where('causer_id', $request->user_id);
            }
        }

        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        // Get per_page from request or cookie, default to 50
        $perPage = $request->input('per_page', $request->cookie('audit_per_page', 50));
        $perPage = in_array($perPage, [25, 50, 100, 200]) ? $perPage : 50;

        $auditLogs = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'logs' => $auditLogs->map(function ($log) {
                return [
                    'id' => $log->id,
                    'causer_name' => $log->causer->name ?? $log->causer->email ?? 'Systeem',
                    'event' => $log->event,
                    'description' => $log->description,
                    'created_at' => $log->created_at->format('d-m-Y H:i'),
                ];
            }),
            'pagination' => [
                'current_page' => $auditLogs->currentPage(),
                'last_page' => $auditLogs->lastPage(),
                'per_page' => $auditLogs->perPage(),
                'total' => $auditLogs->total(),
                'from' => $auditLogs->firstItem(),
                'to' => $auditLogs->lastItem(),
            ],
        ]);
    }
}
