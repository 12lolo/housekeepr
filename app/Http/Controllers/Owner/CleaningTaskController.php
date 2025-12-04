<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\CleaningTask;
use Illuminate\Http\Request;

class CleaningTaskController extends Controller
{
    public function show(CleaningTask $cleaningTask)
    {
        // Authorize owner can only view tasks for their hotel
        $this->authorize('manage-hotel');

        $hotel = auth()->user()->hotel;

        if ($cleaningTask->room->hotel_id !== $hotel->id) {
            abort(403, 'This action is unauthorized.');
        }

        $cleaningTask->load(['room', 'cleaner.user', 'booking', 'taskLogs']);

        return view('owner.cleaning-tasks.show', compact('cleaningTask'));
    }
}
