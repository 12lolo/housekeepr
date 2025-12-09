<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CleaningTask;
use Illuminate\Http\Request;

class CleaningTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'cleaner') {
            // Cleaners see only their own tasks
            $tasks = CleaningTask::where('cleaner_id', $user->cleaner->id)
                ->with(['room', 'booking'])
                ->orderBy('date')
                ->orderBy('suggested_start_time')
                ->paginate(20);
        } else {
            // Owners see all tasks for their hotel
            $hotel = $user->role === 'owner' ? $user->hotel : $user->cleaner->hotel;
            $tasks = CleaningTask::whereHas('room', function ($query) use ($hotel) {
                $query->where('hotel_id', $hotel->id);
            })
                ->with(['room', 'cleaner.user', 'booking'])
                ->orderBy('date', 'desc')
                ->orderBy('suggested_start_time')
                ->paginate(20);
        }

        return response()->json($tasks);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, CleaningTask $cleaningTask)
    {
        $user = $request->user();

        if ($user->role === 'cleaner' && $cleaningTask->cleaner_id !== $user->cleaner->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (in_array($user->role, ['owner', 'authed-user'])) {
            $hotel = $user->hotel;
            if ($cleaningTask->room->hotel_id !== $hotel->id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        }

        return response()->json($cleaningTask->load(['room', 'cleaner.user', 'booking', 'taskLogs']));
    }

    /**
     * Start a cleaning task.
     */
    public function start(Request $request, CleaningTask $cleaningTask)
    {
        $user = $request->user();

        if ($user->role !== 'cleaner' || $cleaningTask->cleaner_id !== $user->cleaner->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!$cleaningTask->isPending()) {
            return response()->json(['message' => 'Task is not pending'], 400);
        }

        $cleaningTask->start();

        return response()->json([
            'message' => 'Task started successfully',
            'task' => $cleaningTask->fresh()->load(['room', 'booking'])
        ]);
    }

    /**
     * Stop a cleaning task (pause).
     */
    public function stop(Request $request, CleaningTask $cleaningTask)
    {
        $user = $request->user();

        if ($user->role !== 'cleaner' || $cleaningTask->cleaner_id !== $user->cleaner->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!$cleaningTask->isInProgress()) {
            return response()->json(['message' => 'Task is not in progress'], 400);
        }

        $cleaningTask->stop();

        return response()->json([
            'message' => 'Task stopped successfully',
            'task' => $cleaningTask->fresh()->load(['room', 'booking'])
        ]);
    }

    /**
     * Complete a cleaning task.
     */
    public function complete(Request $request, CleaningTask $cleaningTask)
    {
        $user = $request->user();

        if ($user->role !== 'cleaner' || $cleaningTask->cleaner_id !== $user->cleaner->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($cleaningTask->isPending()) {
            return response()->json(['message' => 'Task must be started before completion'], 400);
        }

        if ($cleaningTask->isCompleted()) {
            return response()->json(['message' => 'Task is already completed'], 400);
        }

        $cleaningTask->complete();

        return response()->json([
            'message' => 'Task completed successfully',
            'task' => $cleaningTask->fresh()->load(['room', 'booking'])
        ]);
    }
}
