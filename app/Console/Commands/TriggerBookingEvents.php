<?php

namespace App\Console\Commands;

use App\Events\BookingCreated;
use App\Models\Booking;
use App\Models\Hotel;
use Illuminate\Console\Command;

class TriggerBookingEvents extends Command
{
    protected $signature = 'housekeepr:trigger-booking-events {hotel_id? : The ID of the hotel (optional, triggers for all if not provided)}';

    protected $description = 'Manually trigger BookingCreated events for existing bookings without tasks';

    public function handle()
    {
        $hotelId = $this->argument('hotel_id');

        if ($hotelId) {
            $hotel = Hotel::find($hotelId);
            if (! $hotel) {
                $this->error("Hotel with ID {$hotelId} not found!");

                return 1;
            }
            $this->info("Triggering BookingCreated events for {$hotel->name}...");
        } else {
            $this->info('Triggering BookingCreated events for ALL hotels...');
        }

        $query = Booking::whereDoesntHave('cleaningTask');

        if ($hotelId) {
            $query->whereHas('room', function ($q) use ($hotelId) {
                $q->where('hotel_id', $hotelId);
            });
        }

        $bookingsWithoutTasks = $query->with('room.hotel')->get();

        $this->info("Found {$bookingsWithoutTasks->count()} bookings without cleaning tasks");

        if ($bookingsWithoutTasks->isEmpty()) {
            $this->warn('No bookings found that need cleaning tasks.');

            return 0;
        }

        $triggered = 0;
        foreach ($bookingsWithoutTasks as $booking) {
            event(new BookingCreated($booking));
            $triggered++;
            $hotelName = $booking->room->hotel->name ?? 'Unknown';
            $this->info("  ✓ Triggered event for booking: {$booking->guest_name} in room {$booking->room->room_number} ({$hotelName})");
        }

        $this->newLine();
        $this->info("✅ Complete! Triggered {$triggered} events");

        return 0;
    }
}
