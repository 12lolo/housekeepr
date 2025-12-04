<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\DayCapacity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

class DayCapacityController extends Controller
{
    /**
     * Display capacity planning interface (UC-CAP1).
     */
    public function index()
    {
        Gate::authorize('manage-hotel');

        $user = auth()->user();
        $hotel = $user->role === 'owner' ? $user->hotel : $user->cleaner->hotel;

        // Get capacities for next 30 days
        $capacities = DayCapacity::where('hotel_id', $hotel->id)
            ->where('date', '>=', today())
            ->where('date', '<=', today()->addDays(30))
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Get active cleaners count
        $totalCleaners = $hotel->cleaners()->where('status', 'active')->count();

        return view('owner.capacity.index', compact('capacities', 'totalCleaners'));
    }

    /**
     * Store or update capacity for a specific date (UC-CAP1).
     */
    public function store(Request $request)
    {
        Gate::authorize('manage-hotel');

        $user = auth()->user();
        $hotel = $user->role === 'owner' ? $user->hotel : $user->cleaner->hotel;

        $validated = $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'capacity' => 'required|integer|min:0|max:50',
        ]);

        $capacity = DayCapacity::updateOrCreate(
            [
                'hotel_id' => $hotel->id,
                'date' => $validated['date'],
            ],
            [
                'capacity' => $validated['capacity'],
            ]
        );

        activity()
            ->performedOn($capacity)
            ->causedBy($user)
            ->log('Dagcapaciteit ingesteld: ' . $validated['capacity'] . ' op ' . $validated['date']);

        // Trigger replanning for this date
        event(new \App\Events\CapacityUpdated($capacity));

        return back()->with('success', 'Capaciteit ingesteld voor ' . Carbon::parse($validated['date'])->format('d-m-Y'));
    }

    /**
     * Set capacity for multiple dates at once.
     */
    public function bulkStore(Request $request)
    {
        Gate::authorize('manage-hotel');

        $user = auth()->user();
        $hotel = $user->role === 'owner' ? $user->hotel : $user->cleaner->hotel;

        $validated = $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'capacity' => 'required|integer|min:0|max:50',
        ]);

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $count = 0;

        while ($startDate->lte($endDate)) {
            DayCapacity::updateOrCreate(
                [
                    'hotel_id' => $hotel->id,
                    'date' => $startDate->toDateString(),
                ],
                [
                    'capacity' => $validated['capacity'],
                ]
            );

            $startDate->addDay();
            $count++;
        }

        activity()
            ->causedBy($user)
            ->log('Bulk capaciteit ingesteld: ' . $validated['capacity'] . ' voor ' . $count . ' dagen');

        return back()->with('success', 'Capaciteit ingesteld voor ' . $count . ' dagen.');
    }

    /**
     * Remove capacity setting for a date (resets to 0).
     */
    public function destroy(DayCapacity $capacity)
    {
        Gate::authorize('manage-hotel');

        // Verify ownership
        $user = auth()->user();
        $hotel = $user->role === 'owner' ? $user->hotel : $user->cleaner->hotel;

        if ($capacity->hotel_id !== $hotel->id) {
            abort(403);
        }

        activity()
            ->performedOn($capacity)
            ->causedBy($user)
            ->log('Dagcapaciteit verwijderd voor ' . $capacity->date);

        $capacity->delete();

        return back()->with('success', 'Capaciteit verwijderd.');
    }
}
