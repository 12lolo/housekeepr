<?php

namespace App\Listeners;

use App\Events\BookingCreated;
use App\Mail\UrgentIssueMail;
use App\Models\CleaningTask;
use App\Models\DayCapacity;
use App\Models\Issue;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CreateCleaningTaskForBooking
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(BookingCreated|\App\Events\BookingUpdated $event): void
    {
        $debug = storage_path('logs/listener-debug.txt');
        file_put_contents($debug, date('Y-m-d H:i:s')." - Listener called\n", FILE_APPEND);

        $booking = $event->booking;
        $room = $booking->room;
        $hotel = $room->hotel;

        file_put_contents($debug, date('Y-m-d H:i:s')." - Processing booking {$booking->id} for room {$room->room_number}\n", FILE_APPEND);
        Log::info("CreateCleaningTaskForBooking: Processing booking {$booking->id} for room {$room->room_number}");

        // Check if task already exists for this booking
        $existingTask = $booking->cleaningTask;
        file_put_contents($debug, date('Y-m-d H:i:s').' - Existing task check: '.($existingTask ? "YES (#{$existingTask->id})" : 'NO')."\n", FILE_APPEND);

        if ($existingTask && $event instanceof BookingCreated) {
            file_put_contents($debug, date('Y-m-d H:i:s')." - EXIT: Task already exists\n", FILE_APPEND);
            Log::info("Cleaning task already exists for booking {$booking->id}");

            return;
        }

        // If booking was updated and task exists, update the task if not completed
        if ($existingTask && $event instanceof \App\Events\BookingUpdated) {
            if ($existingTask->status !== 'completed') {
                // Recalculate task details based on updated booking
                $checkInDateTime = \Carbon\Carbon::parse($booking->check_in_datetime);
                $date = $checkInDateTime->toDateString();
                $plannedDuration = $room->standard_duration + 10;
                $suggestedStartTime = $checkInDateTime->copy()->subMinutes($plannedDuration);

                $existingTask->update([
                    'date' => $date,
                    'deadline' => $checkInDateTime,
                    'planned_duration' => $plannedDuration,
                    'suggested_start_time' => $suggestedStartTime,
                ]);

                Log::info("Cleaning task updated for booking {$booking->id}");
            }

            return;
        }

        // Check if room has a blocking issue (kan niet gebruikt)
        $blockingIssue = Issue::where('room_id', $room->id)
            ->where('status', 'open')
            ->where('impact', 'kan_niet_gebruikt')
            ->exists();

        if ($blockingIssue) {
            Log::warning("Room {$room->id} has blocking issue, skipping task creation for booking {$booking->id}");

            return;
        }

        // Calculate task details
        $checkInDateTime = Carbon::parse($booking->check_in_datetime);
        $date = $checkInDateTime->toDateString();

        // Skip if check-in date is in the past
        if ($checkInDateTime->isPast()) {
            file_put_contents($debug, date('Y-m-d H:i:s')." - EXIT: Check-in date {$date} is in the past\n", FILE_APPEND);
            Log::info("Skipping task creation for booking {$booking->id} - check-in date {$date} is in the past");

            return;
        }

        // Planned duration = standard_duration + 10 minutes buffer
        $plannedDuration = $room->standard_duration + 10;

        // Suggested start time = check_in_datetime - planned_duration
        $suggestedStartTime = $checkInDateTime->copy()->subMinutes($plannedDuration);

        // Get day capacity
        $dayCapacity = DayCapacity::where('hotel_id', $hotel->id)
            ->whereDate('date', $date)
            ->first();

        file_put_contents($debug, date('Y-m-d H:i:s')." - Day capacity for {$date}: ".($dayCapacity ? $dayCapacity->capacity : 'NONE')."\n", FILE_APPEND);

        if (! $dayCapacity || $dayCapacity->capacity == 0) {
            file_put_contents($debug, date('Y-m-d H:i:s')." - EXIT: No capacity for {$date}\n", FILE_APPEND);
            // No capacity set for this day - create urgent issue and send email
            $this->createUrgentIssue($room, $booking, 'Geen schoonmakers beschikbaar op '.$date);

            return;
        }

        // Check if there's enough time (at least 1 hour before check-in)
        $now = now();
        $hourBeforeCheckIn = $checkInDateTime->copy()->subHour();

        if ($suggestedStartTime->lessThan($now)) {
            // Not enough time - create urgent issue and log warning but still create task
            Log::warning("Urgent: Not enough time for cleaning before check-in at {$checkInDateTime->format('H:i')} for booking {$booking->id}");
            // Still create the task so cleaners can see it, but log the urgency
        }

        // Get active cleaners for this hotel who are available on this day of the week
        $dayOfWeek = Carbon::parse($date)->dayOfWeek; // 0=Sunday, 1=Monday, ..., 6=Saturday
        file_put_contents($debug, date('Y-m-d H:i:s')." - Looking for cleaners on day {$dayOfWeek} ({$date})\n", FILE_APPEND);
        Log::info("Checking for cleaners on day $dayOfWeek for hotel {$hotel->id} on date {$date}");

        // Map day of week to column name
        $dayColumn = match ($dayOfWeek) {
            0 => 'works_sunday',
            1 => 'works_monday',
            2 => 'works_tuesday',
            3 => 'works_wednesday',
            4 => 'works_thursday',
            5 => 'works_friday',
            6 => 'works_saturday',
        };

        $cleaners = $hotel->cleaners()
            ->where('status', 'active')
            ->where($dayColumn, true)
            ->get();

        file_put_contents($debug, date('Y-m-d H:i:s')." - Found {$cleaners->count()} available cleaners\n", FILE_APPEND);
        Log::info("Found {$cleaners->count()} available cleaners");

        if ($cleaners->isEmpty()) {
            file_put_contents($debug, date('Y-m-d H:i:s')." - EXIT: No cleaners available on day {$dayOfWeek}\n", FILE_APPEND);
            $dayName = Carbon::parse($date)->locale('nl')->dayName;
            $this->createUrgentIssue($room, $booking, "Geen schoonmakers beschikbaar op {$dayName} ({$date})");

            return;
        }

        // Assign to cleaner with least tasks on this date (simple load balancing)
        $assignedCleaner = $cleaners->sortBy(function ($cleaner) use ($date) {
            return $cleaner->cleaningTasks()
                ->where('date', $date)
                ->count();
        })->first();

        file_put_contents($debug, date('Y-m-d H:i:s')." - Assigning to cleaner #{$assignedCleaner->id} ({$assignedCleaner->user->name})\n", FILE_APPEND);

        // Create the cleaning task
        $task = CleaningTask::create([
            'room_id' => $room->id,
            'cleaner_id' => $assignedCleaner->id,
            'booking_id' => $booking->id,
            'date' => $date,
            'deadline' => $checkInDateTime,
            'planned_duration' => $plannedDuration,
            'suggested_start_time' => $suggestedStartTime,
            'status' => 'pending',
        ]);

        file_put_contents($debug, date('Y-m-d H:i:s')." - SUCCESS: Created task #{$task->id}\n", FILE_APPEND);

        activity()
            ->performedOn($booking)
            ->log('Schoonmaaktaak automatisch aangemaakt');

        Log::info("Cleaning task created for booking {$booking->id}, assigned to cleaner {$assignedCleaner->id}");
    }

    /**
     * Create urgent issue and send email notification
     */
    protected function createUrgentIssue($room, $booking, $message)
    {
        $issue = Issue::create([
            'room_id' => $room->id,
            'reported_by' => \App\Models\User::getSystemUserId(),
            'impact' => 'graag_snel', // Changed from 'kan_niet_gebruikt' - planning issues should not block the room
            'note' => "URGENT: {$message} voor boeking op ".$booking->check_in_datetime->format('d-m-Y H:i'),
            'status' => 'open',
        ]);

        // Send email to hotel owner
        $owner = $room->hotel->owner;

        try {
            Mail::to($owner->email)->send(new UrgentIssueMail($issue, $booking));
        } catch (\Exception $e) {
            Log::error('Failed to send urgent issue email: '.$e->getMessage());
        }

        activity()
            ->performedOn($issue)
            ->log('Urgent probleem automatisch aangemaakt door systeem');

        Log::warning("Urgent issue created for room {$room->id}: {$message}");
    }
}
