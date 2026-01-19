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
            'guest_name' => 'required|string|max:255',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Verify room belongs to user's hotel
        $user = auth()->user();
        $hotel = $user->role === 'owner' ? $user->hotel : $user->cleaner->hotel;

        $room = Room::findOrFail($validated['room_id']);

        if ($room->hotel_id !== $hotel->id) {
            abort(403, 'Unauthorized access to this room');
        }

        // Check for overlapping bookings (double-booking prevention)
        $checkInDate = \Carbon\Carbon::parse($validated['check_in']);
        $checkOutDate = \Carbon\Carbon::parse($validated['check_out']);

        $overlappingBooking = Booking::where('room_id', $validated['room_id'])
            ->where(function ($query) use ($checkInDate, $checkOutDate) {
                $query->where(function ($q) use ($checkInDate) {
                    // New booking starts during existing booking
                    $q->whereDate('check_in', '<=', $checkInDate)
                        ->whereDate('check_out', '>', $checkInDate);
                })->orWhere(function ($q) use ($checkOutDate) {
                    // New booking ends during existing booking
                    $q->whereDate('check_in', '<', $checkOutDate)
                        ->whereDate('check_out', '>=', $checkOutDate);
                })->orWhere(function ($q) use ($checkInDate, $checkOutDate) {
                    // New booking completely contains existing booking
                    $q->whereDate('check_in', '>=', $checkInDate)
                        ->whereDate('check_out', '<=', $checkOutDate);
                });
            })
            ->exists();

        if ($overlappingBooking) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Deze kamer is al geboekt voor de geselecteerde periode.',
                ], 422);
            }

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['check_in' => 'Deze kamer is al geboekt voor de geselecteerde periode.']);
        }

        // Create datetime fields from dates and room times

        // Use room's check-in and check-out times
        $checkInTime = $room->checkin_time ?? '15:00';
        $checkOutTime = $room->checkout_time ?? '11:00';

        $validated['check_in_datetime'] = $checkInDate->format('Y-m-d').' '.$checkInTime;
        $validated['check_out_datetime'] = $checkOutDate->format('Y-m-d').' '.$checkOutTime;

        $booking = Booking::create($validated);

        activity()
            ->performedOn($booking)
            ->causedBy($user)
            ->log('Boeking aangemaakt');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Boeking succesvol aangemaakt.',
                'booking' => $booking->load('room'),
            ]);
        }

        return redirect()
            ->route('owner.dashboard')
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
            'guest_name' => 'required|string|max:255',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Verify new room belongs to hotel
        $room = Room::findOrFail($validated['room_id']);
        if ($room->hotel_id !== $hotel->id) {
            abort(403);
        }

        // Check for overlapping bookings (double-booking prevention)
        $checkInDate = \Carbon\Carbon::parse($validated['check_in']);
        $checkOutDate = \Carbon\Carbon::parse($validated['check_out']);

        $overlappingBooking = Booking::where('room_id', $validated['room_id'])
            ->where('id', '!=', $booking->id) // Exclude current booking
            ->where(function ($query) use ($checkInDate, $checkOutDate) {
                $query->where(function ($q) use ($checkInDate) {
                    // New booking starts during existing booking
                    $q->whereDate('check_in', '<=', $checkInDate)
                        ->whereDate('check_out', '>', $checkInDate);
                })->orWhere(function ($q) use ($checkOutDate) {
                    // New booking ends during existing booking
                    $q->whereDate('check_in', '<', $checkOutDate)
                        ->whereDate('check_out', '>=', $checkOutDate);
                })->orWhere(function ($q) use ($checkInDate, $checkOutDate) {
                    // New booking completely contains existing booking
                    $q->whereDate('check_in', '>=', $checkInDate)
                        ->whereDate('check_out', '<=', $checkOutDate);
                });
            })
            ->exists();

        if ($overlappingBooking) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Deze kamer is al geboekt voor de geselecteerde periode.',
                ], 422);
            }

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['check_in' => 'Deze kamer is al geboekt voor de geselecteerde periode.']);
        }

        // Update datetime fields

        $checkInTime = $room->checkin_time ?? '15:00';
        $checkOutTime = $room->checkout_time ?? '11:00';

        $validated['check_in_datetime'] = $checkInDate->format('Y-m-d').' '.$checkInTime;
        $validated['check_out_datetime'] = $checkOutDate->format('Y-m-d').' '.$checkOutTime;

        $booking->update($validated);

        activity()
            ->performedOn($booking)
            ->causedBy($user)
            ->log('Boeking bijgewerkt');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Boeking bijgewerkt.',
                'booking' => $booking->load('room'),
            ]);
        }

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
    public function destroy(Request $request, Booking $booking)
    {
        Gate::authorize('manage-hotel');

        // Verify ownership
        $user = auth()->user();
        $hotel = $user->role === 'owner' ? $user->hotel : $user->cleaner->hotel;

        if ($booking->room->hotel_id !== $hotel->id) {
            abort(403);
        }

        $booking->delete();

        activity()
            ->performedOn($booking)
            ->causedBy($user)
            ->log('Boeking verwijderd');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Boeking verwijderd.',
            ]);
        }

        return redirect()
            ->route('owner.dashboard')
            ->with('success', 'Boeking verwijderd.');
    }
}
