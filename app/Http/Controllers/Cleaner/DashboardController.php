<?php

namespace App\Http\Controllers\Cleaner;

use App\Http\Controllers\Controller;
use App\Models\Cleaner;
use App\Models\CleaningTask;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $cleaner = $user->cleaner;

        if (!$cleaner) {
            return view('cleaner.no-assignment');
        }

        $tasks_today = CleaningTask::with(['room', 'booking'])
            ->where('cleaner_id', $cleaner->id)
            ->whereDate('date', today())
            ->orderBy('suggested_start_time')
            ->get();

        $stats = [
            'total_today' => $tasks_today->count(),
            'completed' => $tasks_today->where('status', 'completed')->count(),
            'in_progress' => $tasks_today->where('status', 'in_progress')->count(),
            'pending' => $tasks_today->where('status', 'pending')->count(),
        ];

        return view('cleaner.dashboard', compact('tasks_today', 'stats', 'cleaner'));
    }
}
