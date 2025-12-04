<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Hotel;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
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

        // Placeholder for audit logs - can be implemented later
        $auditLogs = collect([]);

        return view('admin.dashboard-accordion', compact('stats', 'recentHotels', 'owners', 'auditLogs'));
    }
}
