<?php

namespace App\Http\Controllers\Cleaner;

use App\Http\Controllers\Controller;
use App\Models\CleaningTask;
use App\Models\TaskLog;
use Carbon\Carbon;

class TaskController extends Controller
{
    /**
     * Display a listing of cleaner's tasks.
     */
    public function index()
    {
        $user = auth()->user();
        $cleaner = $user->cleaner;

        if (! $cleaner) {
            abort(403, 'No cleaner profile found');
        }

        $tasks_today = CleaningTask::where('cleaner_id', $cleaner->id)
            ->where('date', today())
            ->with(['room', 'booking', 'taskLogs'])
            ->orderBy('suggested_start_time')
            ->get();

        $stats = [
            'completed' => $tasks_today->where('status', 'completed')->count(),
            'in_progress' => $tasks_today->where('status', 'in_progress')->count(),
            'pending' => $tasks_today->where('status', 'pending')->count(),
            'total_today' => $tasks_today->count(),
        ];

        return view('cleaner.dashboard', compact('tasks_today', 'stats'));
    }

    /**
     * Show NFC room clock-in page.
     */
    public function showRoomClockIn(string $room_number)
    {
        $user = auth()->user();
        $cleaner = $user->cleaner;

        if (! $cleaner) {
            abort(403, 'No cleaner profile found');
        }

        // Find the cleaner's task for this room today
        $task = CleaningTask::where('cleaner_id', $cleaner->id)
            ->whereDate('date', today())
            ->whereHas('room', function ($query) use ($room_number, $cleaner) {
                $query->where('room_number', $room_number)
                    ->where('hotel_id', $cleaner->hotel_id);
            })
            ->with(['room', 'booking'])
            ->first();

        // No task found for this room
        if (! $task) {
            return view('cleaner.room-clock-in', [
                'error' => 'no_task',
                'room_number' => $room_number,
                'task' => null,
            ]);
        }

        // Task already completed
        if ($task->status === 'completed') {
            return view('cleaner.room-clock-in', [
                'error' => 'already_completed',
                'room_number' => $room_number,
                'task' => $task,
            ]);
        }

        // Task already in progress
        if ($task->status === 'in_progress') {
            return view('cleaner.room-clock-in', [
                'error' => 'already_started',
                'room_number' => $room_number,
                'task' => $task,
            ]);
        }

        // Show confirmation page for pending task
        return view('cleaner.room-clock-in', [
            'error' => null,
            'room_number' => $room_number,
            'task' => $task,
        ]);
    }

    /**
     * Display task details.
     */
    public function show(CleaningTask $task)
    {
        $user = auth()->user();
        $cleaner = $user->cleaner;

        // Verify task belongs to this cleaner
        if ($task->cleaner_id !== $cleaner->id) {
            abort(403, 'This task is not assigned to you');
        }

        $task->load(['room', 'booking', 'taskLogs']);

        return view('cleaner.tasks.show', compact('task'));
    }

    /**
     * Start a cleaning task (UC-C3).
     */
    public function start(CleaningTask $task)
    {
        $user = auth()->user();
        $cleaner = $user->cleaner;

        // Verify task belongs to this cleaner
        if ($task->cleaner_id !== $cleaner->id) {
            abort(403);
        }

        // Verify task is not already started or completed
        if ($task->status !== 'pending') {
            return back()->with('error', 'Deze taak is al gestart of voltooid.');
        }

        // Update task status and record start time
        $task->update([
            'status' => 'in_progress',
            'actual_start_time' => now(),
        ]);

        // Log the action
        TaskLog::create([
            'cleaning_task_id' => $task->id,
            'action' => 'started',
            'timestamp' => now(),
        ]);

        activity()
            ->performedOn($task)
            ->causedBy($user)
            ->log('Taak gestart');

        return back()->with('success', 'Taak gestart om '.now()->format('H:i'));
    }

    /**
     * Stop a cleaning task (pause) (UC-C4).
     */
    public function stop(CleaningTask $task)
    {
        $user = auth()->user();
        $cleaner = $user->cleaner;

        // Verify task belongs to this cleaner
        if ($task->cleaner_id !== $cleaner->id) {
            abort(403);
        }

        // Verify task is in progress
        if ($task->status !== 'in_progress') {
            return back()->with('error', 'Deze taak is niet bezig.');
        }

        // Check if already stopped (prevent multiple stops without resume)
        $lastLog = $task->taskLogs()->latest('timestamp')->first();
        if ($lastLog && $lastLog->action === 'stopped') {
            return back()->with('error', 'Deze taak is al gepauzeerd. Start opnieuw om door te gaan.');
        }

        // Log the stop action
        TaskLog::create([
            'cleaning_task_id' => $task->id,
            'action' => 'stopped',
            'timestamp' => now(),
        ]);

        activity()
            ->performedOn($task)
            ->causedBy($user)
            ->log('Taak gestopt (pauze)');

        return back()->with('success', 'Taak gepauzeerd om '.now()->format('H:i'));
    }

    /**
     * Complete a cleaning task (UC-C5).
     */
    public function complete(CleaningTask $task)
    {
        $user = auth()->user();
        $cleaner = $user->cleaner;

        // Verify task belongs to this cleaner
        if ($task->cleaner_id !== $cleaner->id) {
            abort(403);
        }

        // Verify task is in progress
        if ($task->status !== 'in_progress') {
            return back()->with('error', 'Deze taak moet eerst gestart worden.');
        }

        // Verify start time exists
        if (! $task->actual_start_time) {
            return back()->with('error', 'Geen starttijd gevonden voor deze taak.');
        }

        $now = now();
        $startTime = Carbon::parse($task->actual_start_time);

        // Calculate actual duration from start to completion
        // We need to subtract any paused time
        $actualDuration = $this->calculateActualDuration($task, $now);

        // Update task
        $task->update([
            'status' => 'completed',
            'actual_end_time' => $now,
            'actual_duration' => $actualDuration,
        ]);

        // Log the completion
        TaskLog::create([
            'cleaning_task_id' => $task->id,
            'action' => 'completed',
            'timestamp' => $now,
        ]);

        activity()
            ->performedOn($task)
            ->causedBy($user)
            ->log('Taak voltooid');

        return back()->with('success', 'Taak voltooid! Duur: '.$actualDuration.' minuten.');
    }

    /**
     * Calculate actual duration accounting for pauses.
     */
    protected function calculateActualDuration(CleaningTask $task, $endTime)
    {
        $logs = $task->taskLogs()->orderBy('timestamp')->get();

        $totalMinutes = 0;
        $lastStartTime = Carbon::parse($task->actual_start_time);

        foreach ($logs as $log) {
            $logTime = Carbon::parse($log->timestamp);

            if ($log->action === 'stopped') {
                // Calculate time from last start to this stop
                $totalMinutes += $lastStartTime->diffInMinutes($logTime);
            } elseif ($log->action === 'started' && $log->timestamp != $task->actual_start_time) {
                // This is a resume after a pause
                $lastStartTime = $logTime;
            }
        }

        // Add time from last start (or resume) to completion
        $totalMinutes += $lastStartTime->diffInMinutes($endTime);

        return round($totalMinutes);
    }
}
