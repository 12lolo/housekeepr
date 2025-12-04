<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RoomController extends Controller
{
    /**
     * Display a listing of rooms (UC-R1).
     */
    public function index()
    {
        Gate::authorize('manage-hotel');

        $user = auth()->user();
        $hotel = $user->role === 'owner' ? $user->hotel : $user->cleaner->hotel;

        $rooms = $hotel->rooms()
            ->withCount(['cleaningTasks' => function ($query) {
                $query->where('date', '>=', today());
            }])
            ->orderBy('room_number')
            ->paginate(20);

        return view('owner.rooms.index', compact('rooms'));
    }

    /**
     * Show the form for creating a new room (UC-R2).
     */
    public function create()
    {
        Gate::authorize('manage-hotel');

        return view('owner.rooms.create');
    }

    /**
     * Store a newly created room (UC-R3 through UC-R9).
     */
    public function store(Request $request)
    {
        Gate::authorize('manage-hotel');

        $user = auth()->user();
        $hotel = $user->role === 'owner' ? $user->hotel : $user->cleaner->hotel;

        // UC-R3: Validate unique room number within hotel
        // UC-R4: Validate room type
        // UC-R5: Validate standard duration
        $validated = $request->validate([
            'room_number' => [
                'required',
                'string',
                'max:50',
                function ($attribute, $value, $fail) use ($hotel) {
                    if (Room::where('hotel_id', $hotel->id)->where('room_number', $value)->exists()) {
                        $fail('Dit kamernummer is al in gebruik.');
                    }
                },
            ],
            'room_type' => 'nullable|string|max:100',
            'standard_duration' => 'required|integer|min:1|max:480', // Max 8 hours
        ]);

        // Create room
        $room = Room::create([
            'hotel_id' => $hotel->id,
            'room_number' => $validated['room_number'],
            'room_type' => $validated['room_type'],
            'standard_duration' => $validated['standard_duration'],
        ]);

        activity()
            ->performedOn($room)
            ->causedBy($user)
            ->log('Kamer aangemaakt');

        return redirect()
            ->route('owner.rooms.index')
            ->with('success', 'Kamer succesvol aangemaakt.');
    }

    /**
     * Display the specified room.
     */
    public function show(Room $room)
    {
        Gate::authorize('manage-hotel');

        // Verify ownership
        $user = auth()->user();
        $hotel = $user->role === 'owner' ? $user->hotel : $user->cleaner->hotel;

        if ($room->hotel_id !== $hotel->id) {
            abort(403);
        }

        $room->load([
            'cleaningTasks' => function ($query) {
                $query->where('date', '>=', today())
                    ->with(['cleaner.user', 'booking'])
                    ->orderBy('date');
            },
            'issues' => function ($query) {
                $query->where('status', 'open')
                    ->with('reportedBy')
                    ->orderBy('created_at', 'desc');
            }
        ]);

        return view('owner.rooms.show', compact('room'));
    }

    /**
     * Show the form for editing the specified room (UC-R10).
     */
    public function edit(Room $room)
    {
        Gate::authorize('manage-hotel');

        // Verify ownership
        $user = auth()->user();
        $hotel = $user->role === 'owner' ? $user->hotel : $user->cleaner->hotel;

        if ($room->hotel_id !== $hotel->id) {
            abort(403);
        }

        return view('owner.rooms.edit', compact('room'));
    }

    /**
     * Update the specified room (UC-R10).
     */
    public function update(Request $request, Room $room)
    {
        Gate::authorize('manage-hotel');

        // Verify ownership
        $user = auth()->user();
        $hotel = $user->role === 'owner' ? $user->hotel : $user->cleaner->hotel;

        if ($room->hotel_id !== $hotel->id) {
            abort(403);
        }

        $validated = $request->validate([
            'room_number' => [
                'required',
                'string',
                'max:50',
                function ($attribute, $value, $fail) use ($hotel, $room) {
                    if (Room::where('hotel_id', $hotel->id)
                        ->where('room_number', $value)
                        ->where('id', '!=', $room->id)
                        ->exists()) {
                        $fail('Dit kamernummer is al in gebruik.');
                    }
                },
            ],
            'room_type' => 'nullable|string|max:100',
            'standard_duration' => 'required|integer|min:1|max:480',
        ]);

        $room->update($validated);

        activity()
            ->performedOn($room)
            ->causedBy($user)
            ->log('Kamer bijgewerkt');

        return redirect()
            ->route('owner.rooms.index')
            ->with('success', 'Kamer bijgewerkt.');
    }

    /**
     * Remove the specified room (UC-R11).
     */
    public function destroy(Room $room)
    {
        Gate::authorize('manage-hotel');

        // Verify ownership
        $user = auth()->user();
        $hotel = $user->role === 'owner' ? $user->hotel : $user->cleaner->hotel;

        if ($room->hotel_id !== $hotel->id) {
            abort(403);
        }

        // Check for active tasks
        $activeTasks = $room->cleaningTasks()
            ->where('status', '!=', 'completed')
            ->where('date', '>=', today())
            ->count();

        if ($activeTasks > 0) {
            return back()->with('error', 'Kan kamer niet verwijderen: er zijn nog actieve taken.');
        }

        activity()
            ->performedOn($room)
            ->causedBy($user)
            ->log('Kamer verwijderd');

        $room->delete();

        return redirect()
            ->route('owner.rooms.index')
            ->with('success', 'Kamer verwijderd.');
    }
}
