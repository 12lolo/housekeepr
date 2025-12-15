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
            $query->where('causer_id', $request->user_id);
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

        $auditLogs = $query->paginate(50);

        // Get unique causers for filter dropdown
        $causers = User::whereIn('id', Activity::distinct()->pluck('causer_id'))
            ->get(['id', 'name', 'email']);

        return view('admin.dashboard-accordion', compact('stats', 'recentHotels', 'owners', 'auditLogs', 'causers'));
    }
}
