<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Issue;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class IssueController extends Controller
{
    /**
     * Display a listing of issues.
     */
    public function index()
    {
        Gate::authorize('manage-hotel');

        $user = auth()->user();
        $hotel = $user->role === 'owner' ? $user->hotel : $user->cleaner->hotel;

        $issues = Issue::whereHas('room', function ($query) use ($hotel) {
            $query->where('hotel_id', $hotel->id);
        })
            ->with(['room', 'reportedBy'])
            ->orderBy('status', 'asc') // Open first
            ->orderBy('impact', 'desc') // Urgent first
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('owner.issues.index', compact('issues'));
    }

    /**
     * Show the form for creating a new issue.
     */
    public function create()
    {
        Gate::authorize('manage-hotel');

        $user = auth()->user();
        $hotel = $user->role === 'owner' ? $user->hotel : $user->cleaner->hotel;

        $rooms = $hotel->rooms()->orderBy('room_number')->get();

        return view('owner.issues.create', compact('rooms'));
    }

    /**
     * Store a newly created issue (UC-I1).
     */
    public function store(Request $request)
    {
        Gate::authorize('manage-hotel');

        $user = auth()->user();
        $hotel = $user->role === 'owner' ? $user->hotel : $user->cleaner->hotel;

        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'impact' => 'required|in:geen_haast,graag_snel,kan_niet_gebruikt',
            'note' => 'required|string|max:1000',
        ]);

        // Verify room belongs to hotel
        $room = Room::findOrFail($validated['room_id']);
        if ($room->hotel_id !== $hotel->id) {
            abort(403);
        }

        $issue = Issue::create([
            'room_id' => $validated['room_id'],
            'reported_by' => $user->id,
            'impact' => $validated['impact'],
            'note' => $validated['note'],
            'status' => 'open',
        ]);

        activity()
            ->performedOn($issue)
            ->causedBy($user)
            ->log('Probleem gemeld: ' . $validated['impact']);

        // If blocking issue, trigger replanning
        if ($validated['impact'] === 'kan_niet_gebruikt') {
            event(new \App\Events\BlockingIssueCreated($issue));
        }

        return redirect()
            ->route('owner.issues.index')
            ->with('success', 'Probleem gemeld.');
    }

    /**
     * Display the specified issue.
     */
    public function show(Issue $issue)
    {
        Gate::authorize('manage-hotel');

        // Verify ownership
        $user = auth()->user();
        $hotel = $user->role === 'owner' ? $user->hotel : $user->cleaner->hotel;

        if ($issue->room->hotel_id !== $hotel->id) {
            abort(403);
        }

        $issue->load(['room', 'reportedBy']);

        return view('owner.issues.show', compact('issue'));
    }

    /**
     * Mark issue as fixed (UC-I2).
     */
    public function markFixed(Issue $issue)
    {
        Gate::authorize('manage-hotel');

        // Verify ownership
        $user = auth()->user();
        $hotel = $user->role === 'owner' ? $user->hotel : $user->cleaner->hotel;

        if ($issue->room->hotel_id !== $hotel->id) {
            abort(403);
        }

        if ($issue->status === 'gefixt') {
            return back()->with('error', 'Dit probleem is al gefixt.');
        }

        $wasBlocking = $issue->impact === 'kan_niet_gebruikt';

        $issue->update(['status' => 'gefixt']);

        activity()
            ->performedOn($issue)
            ->causedBy($user)
            ->log('Probleem gemarkeerd als gefixt');

        // If it was a blocking issue, trigger replanning
        if ($wasBlocking) {
            event(new \App\Events\IssueFixed($issue));
        }

        return back()->with('success', 'Probleem gemarkeerd als gefixt. Kamer is weer planbaar.');
    }

    /**
     * Reopen a fixed issue.
     */
    public function reopen(Issue $issue)
    {
        Gate::authorize('manage-hotel');

        // Verify ownership
        $user = auth()->user();
        $hotel = $user->role === 'owner' ? $user->hotel : $user->cleaner->hotel;

        if ($issue->room->hotel_id !== $hotel->id) {
            abort(403);
        }

        if ($issue->status === 'open') {
            return back()->with('error', 'Dit probleem is al open.');
        }

        $issue->update(['status' => 'open']);

        activity()
            ->performedOn($issue)
            ->causedBy($user)
            ->log('Probleem heropend');

        return back()->with('success', 'Probleem heropend.');
    }

    /**
     * Remove the specified issue.
     */
    public function destroy(Issue $issue)
    {
        Gate::authorize('manage-hotel');

        // Verify ownership
        $user = auth()->user();
        $hotel = $user->role === 'owner' ? $user->hotel : $user->cleaner->hotel;

        if ($issue->room->hotel_id !== $hotel->id) {
            abort(403);
        }

        activity()
            ->performedOn($issue)
            ->causedBy($user)
            ->log('Probleem verwijderd');

        $issue->delete();

        return redirect()
            ->route('owner.issues.index')
            ->with('success', 'Probleem verwijderd.');
    }
}
