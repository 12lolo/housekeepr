<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize('manage-hotel');

        $user = $request->user();
        $hotel = $user->role === 'owner' ? $user->hotel : $user->cleaner->hotel;

        $bookings = Booking::whereHas('room', function ($query) use ($hotel) {
            $query->where('hotel_id', $hotel->id);
        })
            ->with(['room', 'cleaningTask.cleaner.user'])
            ->orderBy('check_in_datetime', 'desc')
            ->paginate(20);

        return response()->json($bookings);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('manage-hotel');

        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'check_in_datetime' => 'required|date|after:now',
            'notes' => 'nullable|string|max:1000',
        ]);

        $user = $request->user();
        $hotel = $user->role === 'owner' ? $user->hotel : $user->cleaner->hotel;

        $room = Room::findOrFail($validated['room_id']);

        if ($room->hotel_id !== $hotel->id) {
            return response()->json(['message' => 'Unauthorized access to this room'], 403);
        }

        $booking = Booking::create($validated);

        activity()
            ->performedOn($booking)
            ->causedBy($user)
            ->log('Boeking aangemaakt via API');

        return response()->json([
            'message' => 'Booking created successfully',
            'booking' => $booking->load(['room', 'cleaningTask.cleaner.user'])
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Booking $booking)
    {
        Gate::authorize('manage-hotel');

        $user = $request->user();
        $hotel = $user->role === 'owner' ? $user->hotel : $user->cleaner->hotel;

        if ($booking->room->hotel_id !== $hotel->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($booking->load(['room', 'cleaningTask.cleaner.user', 'cleaningTask.taskLogs']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Booking $booking)
    {
        Gate::authorize('manage-hotel');

        $user = $request->user();
        $hotel = $user->role === 'owner' ? $user->hotel : $user->cleaner->hotel;

        if ($booking->room->hotel_id !== $hotel->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'room_id' => 'sometimes|exists:rooms,id',
            'check_in_datetime' => 'sometimes|date|after:now',
            'notes' => 'nullable|string|max:1000',
        ]);

        $booking->update($validated);

        activity()
            ->performedOn($booking)
            ->causedBy($user)
            ->log('Boeking bijgewerkt via API');

        return response()->json([
            'message' => 'Booking updated successfully',
            'booking' => $booking->load(['room', 'cleaningTask.cleaner.user'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Booking $booking)
    {
        Gate::authorize('manage-hotel');

        $user = $request->user();
        $hotel = $user->role === 'owner' ? $user->hotel : $user->cleaner->hotel;

        if ($booking->room->hotel_id !== $hotel->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        activity()
            ->performedOn($booking)
            ->causedBy($user)
            ->log('Boeking verwijderd via API');

        $booking->delete();

        return response()->json(['message' => 'Booking deleted successfully']);
    }
}
