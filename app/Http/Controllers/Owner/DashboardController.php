<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\CleaningTask;
use App\Models\Issue;
use Illuminate\Http\Request;

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

        if (!$hotel) {
            return view('owner.no-hotel');
        }

        $stats = [
            'total_rooms' => $hotel->rooms()->count(),
            'tasks_today' => CleaningTask::whereHas('room', function($q) use ($hotel) {
                $q->where('hotel_id', $hotel->id);
            })->whereDate('date', today())->count(),
            'tasks_pending' => CleaningTask::whereHas('room', function($q) use ($hotel) {
                $q->where('hotel_id', $hotel->id);
            })->where('status', 'pending')->count(),
            'open_issues' => Issue::whereHas('room', function($q) use ($hotel) {
                $q->where('hotel_id', $hotel->id);
            })->where('status', 'open')->count(),
        ];

        $today_tasks = CleaningTask::with(['room', 'cleaner.user', 'booking'])
            ->whereHas('room', function($q) use ($hotel) {
                $q->where('hotel_id', $hotel->id);
            })
            ->whereDate('date', today())
            ->orderBy('suggested_start_time')
            ->get();

        $urgent_issues = Issue::with('room')
            ->whereHas('room', function($q) use ($hotel) {
                $q->where('hotel_id', $hotel->id);
            })
            ->where('status', 'open')
            ->where('impact', 'kan_niet_gebruikt')
            ->latest()
            ->take(5)
            ->get();

        // Fetch additional data for accordion sections
        $rooms = $hotel->rooms()->withCount('bookings')->latest()->get();
        $bookings = $hotel->rooms()->with('bookings.room')->get()->pluck('bookings')->flatten()->sortByDesc('check_in');
        $cleaners = $hotel->cleaners()->with('user')->get();
        $issues = Issue::with('room')->whereHas('room', function($q) use ($hotel) {
            $q->where('hotel_id', $hotel->id);
        })->latest()->get();

        // Fetch cleaning schedule (upcoming and pending tasks)
        $cleaning_schedule = CleaningTask::with(['room', 'cleaner.user', 'booking'])
            ->whereHas('room', function($q) use ($hotel) {
                $q->where('hotel_id', $hotel->id);
            })
            ->where('date', '>=', today())
            ->orderBy('date')
            ->orderBy('suggested_start_time')
            ->get();

        // Check if this is a new hotel that needs onboarding
        $isNewHotel = $hotel->created_at->diffInHours(now()) < 24 && $stats['total_rooms'] === 0;

        return view('owner.dashboard-accordion', compact(
            'stats',
            'today_tasks',
            'urgent_issues',
            'hotel',
            'rooms',
            'bookings',
            'cleaners',
            'issues',
            'cleaning_schedule',
            'isNewHotel'
        ));
    }
}
