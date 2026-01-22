<?php

namespace App\Observers;

use App\Models\Booking;

class BookingObserver
{
    /**
     * Handle the Booking "created" event.
     */
    public function created(Booking $booking): void
    {
        //
    }

    /**
     * Handle the Booking "updated" event.
     */
    public function updated(Booking $booking): void
    {
        //
    }

    /**
     * Handle the Booking "deleted" event.
     * Clean up any auto-generated issues related to this booking.
     */
    public function deleted(Booking $booking): void
    {
        // Clean up auto-generated issues for this room that mention this booking
        \App\Models\Issue::where('room_id', $booking->room_id)
            ->where('note', 'like', '%Boeking #'.$booking->id.'%')
            ->delete();
    }

    /**
     * Handle the Booking "restored" event.
     */
    public function restored(Booking $booking): void
    {
        //
    }

    /**
     * Handle the Booking "force deleted" event.
     */
    public function forceDeleted(Booking $booking): void
    {
        //
    }
}
