<?php

namespace App\Listeners;

use App\Events\BookingCreated;
use App\Models\CleaningTask;
use App\Models\DayCapacity;
use App\Models\Issue;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\UrgentIssueMail;

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
        $booking = $event->booking;
        $room = $booking->room;
        $hotel = $room->hotel;

        // Check if task already exists for this booking
        if ($booking->cleaningTask()->exists()) {
            Log::info("Cleaning task already exists for booking {$booking->id}");
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

        // Planned duration = standard_duration + 10 minutes buffer
        $plannedDuration = $room->standard_duration + 10;

        // Suggested start time = check_in_datetime - planned_duration
        $suggestedStartTime = $checkInDateTime->copy()->subMinutes($plannedDuration);

        // Get day capacity
        $dayCapacity = DayCapacity::where('hotel_id', $hotel->id)
            ->where('date', $date)
            ->first();

        if (!$dayCapacity || $dayCapacity->capacity == 0) {
            // No capacity set for this day - create urgent issue and send email
            $this->createUrgentIssue($room, $booking, 'Geen schoonmakers beschikbaar op ' . $date);
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

        // Get active cleaners for this hotel
        $cleaners = $hotel->cleaners()
            ->where('status', 'active')
            ->get();

        if ($cleaners->isEmpty()) {
            $this->createUrgentIssue($room, $booking, 'Geen actieve schoonmakers beschikbaar');
            return;
        }

        // Assign to cleaner with least tasks on this date (simple load balancing)
        $assignedCleaner = $cleaners->sortBy(function ($cleaner) use ($date) {
            return $cleaner->cleaningTasks()
                ->where('date', $date)
                ->count();
        })->first();

        // Create the cleaning task
        CleaningTask::create([
            'room_id' => $room->id,
            'cleaner_id' => $assignedCleaner->id,
            'booking_id' => $booking->id,
            'date' => $date,
            'deadline' => $checkInDateTime,
            'planned_duration' => $plannedDuration,
            'suggested_start_time' => $suggestedStartTime,
            'status' => 'pending',
        ]);

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
            'reported_by' => 1, // System user
            'impact' => 'kan_niet_gebruikt',
            'note' => "URGENT: {$message} voor boeking op " . $booking->check_in_datetime->format('d-m-Y H:i'),
            'status' => 'open',
        ]);

        // Send email to hotel owner
        $owner = $room->hotel->owner;

        try {
            Mail::to($owner->email)->send(new UrgentIssueMail($issue, $booking));
        } catch (\Exception $e) {
            Log::error("Failed to send urgent issue email: " . $e->getMessage());
        }

        activity()
            ->performedOn($issue)
            ->log('Urgent probleem automatisch aangemaakt door systeem');

        Log::warning("Urgent issue created for room {$room->id}: {$message}");
    }
}
