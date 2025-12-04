<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BookingController extends Controller
{
    /**
     * Display a listing of bookings.
     */
    public function index()
    {
        Gate::authorize('manage-hotel');

        $user = auth()->user();
        $hotel = $user->role === 'owner' ? $user->hotel : $user->cleaner->hotel;

        $bookings = Booking::whereHas('room', function ($query) use ($hotel) {
            $query->where('hotel_id', $hotel->id);
        })
            ->with(['room', 'cleaningTask.cleaner.user'])
            ->orderBy('check_in_datetime', 'desc')
            ->paginate(20);

        return view('owner.bookings.index', compact('bookings'));
    }

    /**
     * Show the form for creating a new booking.
     */
    public function create()
    {
        Gate::authorize('manage-hotel');

        $user = auth()->user();
        $hotel = $user->role === 'owner' ? $user->hotel : $user->cleaner->hotel;

        $rooms = $hotel->rooms()
            ->orderBy('room_number')
            ->get();

        return view('owner.bookings.create', compact('rooms'));
    }

    /**
     * Store a newly created booking in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('manage-hotel');

        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'check_in_datetime' => 'required|date|after:now',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Verify room belongs to user's hotel
        $user = auth()->user();
        $hotel = $user->role === 'owner' ? $user->hotel : $user->cleaner->hotel;

        $room = Room::findOrFail($validated['room_id']);

        if ($room->hotel_id !== $hotel->id) {
            abort(403, 'Unauthorized access to this room');
        }

        $booking = Booking::create($validated);

        activity()
            ->performedOn($booking)
            ->causedBy($user)
            ->log('Boeking aangemaakt');

        return redirect()
            ->route('owner.bookings.index')
            ->with('success', 'Boeking succesvol aangemaakt. Schoonmaaktaak wordt automatisch gepland.');
    }

    /**
     * Display the specified booking.
     */
    public function show(Booking $booking)
    {
        Gate::authorize('manage-hotel');

        // Verify ownership
        $user = auth()->user();
        $hotel = $user->role === 'owner' ? $user->hotel : $user->cleaner->hotel;

        if ($booking->room->hotel_id !== $hotel->id) {
            abort(403);
        }

        $booking->load(['room', 'cleaningTask.cleaner.user', 'cleaningTask.taskLogs']);

        return view('owner.bookings.show', compact('booking'));
    }

    /**
     * Show the form for editing the specified booking.
     */
    public function edit(Booking $booking)
    {
        Gate::authorize('manage-hotel');

        // Verify ownership
        $user = auth()->user();
        $hotel = $user->role === 'owner' ? $user->hotel : $user->cleaner->hotel;

        if ($booking->room->hotel_id !== $hotel->id) {
            abort(403);
        }

        $rooms = $hotel->rooms()
            ->orderBy('room_number')
            ->get();

        return view('owner.bookings.edit', compact('booking', 'rooms'));
    }

    /**
     * Update the specified booking in storage.
     */
    public function update(Request $request, Booking $booking)
    {
        Gate::authorize('manage-hotel');

        // Verify ownership
        $user = auth()->user();
        $hotel = $user->role === 'owner' ? $user->hotel : $user->cleaner->hotel;

        if ($booking->room->hotel_id !== $hotel->id) {
            abort(403);
        }

        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'check_in_datetime' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Verify new room belongs to hotel
        $room = Room::findOrFail($validated['room_id']);
        if ($room->hotel_id !== $hotel->id) {
            abort(403);
        }

        $booking->update($validated);

        activity()
            ->performedOn($booking)
            ->causedBy($user)
            ->log('Boeking bijgewerkt');

        // Trigger replanning if check_in_datetime changed
        if ($booking->wasChanged('check_in_datetime')) {
            event(new \App\Events\BookingUpdated($booking));
        }

        return redirect()
            ->route('owner.bookings.index')
            ->with('success', 'Boeking bijgewerkt.');
    }

    /**
     * Remove the specified booking from storage.
     */
    public function destroy(Booking $booking)
    {
        Gate::authorize('manage-hotel');

        // Verify ownership
        $user = auth()->user();
        $hotel = $user->role === 'owner' ? $user->hotel : $user->cleaner->hotel;

        if ($booking->room->hotel_id !== $hotel->id) {
            abort(403);
        }

        activity()
            ->performedOn($booking)
            ->causedBy($user)
            ->log('Boeking verwijderd');

        $booking->delete();

        return redirect()
            ->route('owner.bookings.index')
            ->with('success', 'Boeking verwijderd.');
    }
}
