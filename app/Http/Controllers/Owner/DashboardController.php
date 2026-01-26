<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\CleaningTask;
use App\Models\Hotel;
use App\Models\Issue;

class DashboardController extends Controller
{
    public function accordion()
    {
        $user = auth()->user();

        // Check if user needs to complete account setup first
        if (empty($user->name)) {
            return redirect()->route('owner.setup.account');
        }

        $hotel = $user->hotel;

        if (! $hotel) {
            return view('owner.no-hotel');
        }

        $stats = [
            'total_rooms' => $hotel->rooms()->count(),
            'tasks_today' => CleaningTask::whereHas('room', function ($q) use ($hotel) {
                $q->where('hotel_id', $hotel->id);
            })->whereDate('date', today())->count(),
            'tasks_pending' => CleaningTask::whereHas('room', function ($q) use ($hotel) {
                $q->where('hotel_id', $hotel->id);
            })->where('status', 'pending')->count(),
            'open_issues' => Issue::whereHas('room', function ($q) use ($hotel) {
                $q->where('hotel_id', $hotel->id);
            })->where('status', 'open')->count(),
        ];

        $today_tasks = CleaningTask::with(['room', 'cleaner.user', 'booking'])
            ->whereHas('room', function ($q) use ($hotel) {
                $q->where('hotel_id', $hotel->id);
            })
            ->whereDate('date', today())
            ->orderBy('suggested_start_time')
            ->get();

        $urgent_issues = Issue::with('room')
            ->whereHas('room', function ($q) use ($hotel) {
                $q->where('hotel_id', $hotel->id);
            })
            ->where('status', 'open')
            ->where('impact', 'kan_niet_gebruikt')
            ->latest()
            ->take(5)
            ->get();

        // Fetch additional data for accordion sections
        $rooms = $hotel->rooms()->withCount('bookings')->latest()->get();
        $cleaners = $hotel->cleaners()->with(['user'])->get();

        // Get all bookings for the hotel
        $allBookings = $hotel->rooms()->with('bookings.room')->get()->pluck('bookings')->flatten();

        // Delete past bookings (check_out before today)
        $allBookings->filter(fn ($booking) => $booking->check_out->lt(today()))->each->delete();

        // Refresh bookings after deletion and split into actief/today/upcoming
        $allBookings = $hotel->rooms()->with('bookings.room')->get()->pluck('bookings')->flatten();

        // Actief: checked in before today, still staying (check_out > today, not today)
        $activeBookings = $allBookings->filter(fn ($booking) => $booking->check_in->lt(today()) && $booking->check_out->gt(today()))->sortBy('check_in');

        // Vandaag: check-in or check-out is today
        $todayBookings = $allBookings->filter(fn ($booking) => $booking->check_in->isToday() || $booking->check_out->isToday())->sortBy('check_in');

        // Aankomend: check-in is in the future (after today)
        $upcomingBookings = $allBookings->filter(fn ($booking) => $booking->check_in->gt(today()))->sortBy('check_in');
        // Delete past issues (before today)
        Issue::whereHas('room', function ($q) use ($hotel) {
            $q->where('hotel_id', $hotel->id);
        })->whereDate('created_at', '<', today())->where('status', 'gefixt')->delete();

        // Get today's issues
        $todayIssues = Issue::with('room')->whereHas('room', function ($q) use ($hotel) {
            $q->where('hotel_id', $hotel->id);
        })->whereDate('created_at', today())->latest()->get();

        // Get upcoming issues (future)
        $upcomingIssues = Issue::with('room')->whereHas('room', function ($q) use ($hotel) {
            $q->where('hotel_id', $hotel->id);
        })->whereDate('created_at', '>', today())->latest()->get();

        // Keep open issues from the past (not fixed yet)
        $openPastIssues = Issue::with('room')->whereHas('room', function ($q) use ($hotel) {
            $q->where('hotel_id', $hotel->id);
        })->whereDate('created_at', '<', today())->where('status', 'open')->latest()->get();

        // Delete past cleaning tasks (before today)
        CleaningTask::whereHas('room', function ($q) use ($hotel) {
            $q->where('hotel_id', $hotel->id);
        })->whereDate('date', '<', today())->delete();

        // Get today's cleaning schedule
        $todayCleaningSchedule = CleaningTask::with(['room', 'cleaner.user', 'booking'])
            ->whereHas('room', function ($q) use ($hotel) {
                $q->where('hotel_id', $hotel->id);
            })
            ->whereDate('date', today())
            ->orderBy('suggested_start_time')
            ->get();

        // Get upcoming cleaning schedule
        $upcomingCleaningSchedule = CleaningTask::with(['room', 'cleaner.user', 'booking'])
            ->whereHas('room', function ($q) use ($hotel) {
                $q->where('hotel_id', $hotel->id);
            })
            ->whereDate('date', '>', today())
            ->orderBy('date')
            ->orderBy('suggested_start_time')
            ->get();

        // Fetch performance data (last 7 days)
        $endDate = today();
        $startDate = $endDate->copy()->subDays(6);

        $performance_tasks = CleaningTask::whereHas('room', function ($query) use ($hotel) {
            $query->where('hotel_id', $hotel->id);
        })
            ->where('status', 'completed')
            ->whereNotNull('actual_duration')
            ->whereBetween('date', [$startDate, $endDate])
            ->with(['room', 'cleaner.user', 'booking'])
            ->orderBy('date', 'desc')
            ->orderBy('actual_end_time', 'desc')
            ->get();

        // Calculate performance metrics per cleaner
        $cleanerPerformance = $performance_tasks->groupBy('cleaner_id')->map(function ($cleanerTasks) {
            $totalTasks = $cleanerTasks->count();
            $totalPlanned = $cleanerTasks->sum('planned_duration');
            $totalActual = $cleanerTasks->sum('actual_duration');
            $variance = $totalActual - $totalPlanned;
            $variancePercent = $totalPlanned > 0
                ? round(($variance / $totalPlanned) * 100, 1)
                : 0;

            $fasterCount = $cleanerTasks->filter(fn ($task) => $task->actual_duration < $task->planned_duration)->count();
            $slowerCount = $cleanerTasks->filter(fn ($task) => $task->actual_duration > $task->planned_duration)->count();

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

        // Check if this is a new hotel that needs onboarding
        $isNewHotel = $hotel->created_at->diffInHours(now()) < 24 && $stats['total_rooms'] === 0;

        return view('owner.dashboard-accordion', compact(
            'stats',
            'today_tasks',
            'urgent_issues',
            'hotel',
            'rooms',
            'activeBookings',
            'todayBookings',
            'upcomingBookings',
            'cleaners',
            'todayIssues',
            'upcomingIssues',
            'openPastIssues',
            'todayCleaningSchedule',
            'upcomingCleaningSchedule',
            'cleanerPerformance',
            'isNewHotel'
        ));
    }
}
