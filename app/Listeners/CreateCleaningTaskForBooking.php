<?php

namespace App\Listeners;

use App\Events\BookingCreated;
use App\Services\CleaningScheduler;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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

        Log::info("CreateCleaningTaskForBooking: Processing booking {$booking->id} for room {$room->room_number}");

        // Get check-in date
        $checkInDateTime = Carbon::parse($booking->check_in_datetime);
        $date = $checkInDateTime->toDateString();

        // Skip if check-in date is in the past
        if ($checkInDateTime->isPast()) {
            Log::info("Skipping task creation for booking {$booking->id} - check-in date {$date} is in the past");

            return;
        }

        // Use intelligent scheduler to schedule all tasks for this date
        // This ensures optimal scheduling considering all bookings and time conflicts
        $scheduler = new CleaningScheduler;
        $stats = $scheduler->scheduleForDate($hotel, $checkInDateTime);

        Log::info("Scheduled tasks for {$date}: {$stats['scheduled']} scheduled, {$stats['conflicts']} conflicts, {$stats['no_cleaner']} no cleaner");

        activity()
            ->performedOn($booking)
            ->log('Schoonmaaktaak automatisch ingepland');
    }
}
