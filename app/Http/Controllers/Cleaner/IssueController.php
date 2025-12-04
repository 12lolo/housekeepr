<?php

namespace App\Http\Controllers\Cleaner;

use App\Http\Controllers\Controller;
use App\Models\Issue;
use App\Models\CleaningTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class IssueController extends Controller
{
    /**
     * Show form for reporting an issue (UC-C6).
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        $cleaner = $user->cleaner;

        if (!$cleaner) {
            abort(403);
        }

        // Optional: get room from task_id
        $task = null;
        if ($request->filled('task_id')) {
            $task = CleaningTask::where('cleaner_id', $cleaner->id)
                ->findOrFail($request->task_id);
        }

        return view('cleaner.issues.create', compact('task'));
    }

    /**
     * Store a newly created issue with optional photo (UC-C6).
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $cleaner = $user->cleaner;

        if (!$cleaner) {
            abort(403);
        }

        $validated = $request->validate([
            'task_id' => 'required|exists:cleaning_tasks,id',
            'impact' => 'required|in:geen_haast,graag_snel,kan_niet_gebruikt',
            'note' => 'required|string|max:1000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // Max 5MB
        ]);

        // Verify task belongs to this cleaner
        $task = CleaningTask::where('cleaner_id', $cleaner->id)
            ->findOrFail($validated['task_id']);

        $photoPath = null;

        // Handle photo upload (UC-C6: optional photo)
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');

            // Store in storage/app/public/issues
            $photoPath = $photo->store('issues', 'public');
        }

        // Create issue
        $issue = Issue::create([
            'room_id' => $task->room_id,
            'reported_by' => $user->id,
            'impact' => $validated['impact'],
            'note' => $validated['note'],
            'photo_path' => $photoPath,
            'status' => 'open',
        ]);

        activity()
            ->performedOn($issue)
            ->causedBy($user)
            ->log('Probleem gemeld door schoonmaker: ' . $validated['impact']);

        // If blocking issue, trigger replanning and send email
        if ($validated['impact'] === 'kan_niet_gebruikt') {
            event(new \App\Events\BlockingIssueCreated($issue));
        }

        return redirect()
            ->route('cleaner.dashboard')
            ->with('success', 'Probleem gemeld. De eigenaar is op de hoogte gesteld.');
    }

    /**
     * Display the specified issue.
     */
    public function show(Issue $issue)
    {
        $user = auth()->user();
        $cleaner = $user->cleaner;

        if (!$cleaner) {
            abort(403);
        }

        // Verify issue is for a room in cleaner's hotel
        if ($issue->room->hotel_id !== $cleaner->hotel_id) {
            abort(403);
        }

        $issue->load(['room', 'reportedBy']);

        return view('cleaner.issues.show', compact('issue'));
    }
}
