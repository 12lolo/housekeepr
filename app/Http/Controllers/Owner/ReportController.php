<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\CleaningTask;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    /**
     * Display daily overview report (UC-REP1).
     */
    public function dailyOverview(Request $request)
    {
        Gate::authorize('manage-hotel');

        $user = auth()->user();
        $hotel = $user->role === 'owner' ? $user->hotel : $user->cleaner->hotel;

        // Get date from request or default to today
        $date = $request->filled('date')
            ? Carbon::parse($request->date)
            : today();

        // Get all tasks for this hotel on the selected date
        $tasks = CleaningTask::whereHas('room', function ($query) use ($hotel) {
            $query->where('hotel_id', $hotel->id);
        })
            ->where('date', $date->toDateString())
            ->with(['room', 'cleaner.user', 'booking'])
            ->orderBy('suggested_start_time')
            ->get();

        // Calculate statistics
        $stats = [
            'total' => $tasks->count(),
            'completed' => $tasks->where('status', 'completed')->count(),
            'in_progress' => $tasks->where('status', 'in_progress')->count(),
            'pending' => $tasks->where('status', 'pending')->count(),
            'total_planned_duration' => $tasks->sum('planned_duration'),
            'total_actual_duration' => $tasks->whereNotNull('actual_duration')->sum('actual_duration'),
            'average_actual_duration' => $tasks->whereNotNull('actual_duration')->avg('actual_duration'),
        ];

        return view('owner.reports.daily-overview', compact('date', 'tasks', 'stats'));
    }

    /**
     * Export tasks to CSV (UC-REP2).
     */
    public function exportCsv(Request $request)
    {
        Gate::authorize('manage-hotel');

        $user = auth()->user();
        $hotel = $user->role === 'owner' ? $user->hotel : $user->cleaner->hotel;

        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);

        // Get tasks in date range
        $tasks = CleaningTask::whereHas('room', function ($query) use ($hotel) {
            $query->where('hotel_id', $hotel->id);
        })
            ->whereBetween('date', [$startDate, $endDate])
            ->with(['room', 'cleaner.user', 'booking'])
            ->orderBy('date')
            ->orderBy('suggested_start_time')
            ->get();

        // Generate CSV
        $filename = "housekeepr-rapport-{$hotel->id}-{$startDate->format('Y-m-d')}-{$endDate->format('Y-m-d')}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($tasks) {
            $file = fopen('php://output', 'w');

            // BOM for Excel UTF-8 support
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // CSV headers
            fputcsv($file, [
                'Datum',
                'Kamer',
                'Schoonmaker',
                'Check-in Tijd',
                'Suggestie Start',
                'Geplande Duur (min)',
                'Werkelijke Start',
                'Werkelijke Eind',
                'Werkelijke Duur (min)',
                'Status',
                'Notities',
            ], ';');

            // Data rows
            foreach ($tasks as $task) {
                fputcsv($file, [
                    $task->date->format('d-m-Y'),
                    $task->room->room_number,
                    $task->cleaner->user->name,
                    $task->booking ? $task->booking->check_in_datetime->format('H:i') : '-',
                    $task->suggested_start_time ? $task->suggested_start_time->format('H:i') : '-',
                    $task->planned_duration,
                    $task->actual_start_time ? $task->actual_start_time->format('d-m-Y H:i') : '-',
                    $task->actual_end_time ? $task->actual_end_time->format('d-m-Y H:i') : '-',
                    $task->actual_duration ?? '-',
                    match ($task->status) {
                        'completed' => 'Voltooid',
                        'in_progress' => 'Bezig',
                        'pending' => 'Wachtend',
                        default => $task->status,
                    },
                    $task->booking?->notes ?? '',
                ], ';');
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Display cleaner performance report.
     */
    public function cleanerPerformance(Request $request)
    {
        Gate::authorize('manage-hotel');

        $user = auth()->user();
        $hotel = $user->role === 'owner' ? $user->hotel : $user->cleaner->hotel;

        // Get date range from request or default to last 7 days
        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)
            : today();

        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)
            : $endDate->copy()->subDays(6);

        // Get all completed tasks for this hotel in the date range
        $tasks = CleaningTask::whereHas('room', function ($query) use ($hotel) {
            $query->where('hotel_id', $hotel->id);
        })
            ->where('status', 'completed')
            ->whereNotNull('actual_duration')
            ->whereBetween('date', [$startDate, $endDate])
            ->with(['room', 'cleaner.user', 'booking'])
            ->orderBy('date', 'desc')
            ->orderBy('actual_end_time', 'desc')
            ->get();

        // Calculate performance metrics per task
        $tasksWithMetrics = $tasks->map(function ($task) {
            $variance = $task->actual_duration - $task->planned_duration;
            $variancePercent = $task->planned_duration > 0
                ? round(($variance / $task->planned_duration) * 100, 1)
                : 0;

            return [
                'task' => $task,
                'variance' => $variance,
                'variance_percent' => $variancePercent,
                'performance' => $variance < 0 ? 'faster' : ($variance > 0 ? 'slower' : 'exact'),
            ];
        });

        // Group by cleaner and calculate aggregate stats
        $cleanerStats = $tasks->groupBy('cleaner_id')->map(function ($cleanerTasks, $cleanerId) {
            $totalTasks = $cleanerTasks->count();
            $totalPlanned = $cleanerTasks->sum('planned_duration');
            $totalActual = $cleanerTasks->sum('actual_duration');
            $variance = $totalActual - $totalPlanned;
            $variancePercent = $totalPlanned > 0
                ? round(($variance / $totalPlanned) * 100, 1)
                : 0;

            $fasterCount = $cleanerTasks->filter(function ($task) {
                return $task->actual_duration < $task->planned_duration;
            })->count();

            $slowerCount = $cleanerTasks->filter(function ($task) {
                return $task->actual_duration > $task->planned_duration;
            })->count();

            return [
                'cleaner' => $cleanerTasks->first()->cleaner,
                'total_tasks' => $totalTasks,
                'total_planned_minutes' => $totalPlanned,
                'total_actual_minutes' => $totalActual,
                'variance_minutes' => $variance,
                'variance_percent' => $variancePercent,
                'faster_count' => $fasterCount,
                'slower_count' => $slowerCount,
                'exact_count' => $totalTasks - $fasterCount - $slowerCount,
                'performance' => $variance < 0 ? 'faster' : ($variance > 0 ? 'slower' : 'exact'),
            ];
        })->sortByDesc('total_tasks');

        return view('owner.reports.cleaner-performance', compact('startDate', 'endDate', 'tasksWithMetrics', 'cleanerStats'));
    }
}
