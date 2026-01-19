<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\CleaningTask;
use App\Models\Hotel;
use Illuminate\Console\Command;

class DebugCleaningSchedule extends Command
{
    protected $signature = 'housekeepr:debug-schedule {hotel_id? : The ID of the hotel (optional, shows all if not provided)}';

    protected $description = 'Debug cleaning schedule and tasks';

    public function handle()
    {
        $hotelId = $this->argument('hotel_id');

        if ($hotelId) {
            $hotel = Hotel::find($hotelId);
            if (! $hotel) {
                $this->error("Hotel with ID {$hotelId} not found!");

                return 1;
            }
            $this->info("=== Cleaning Schedule Debug for {$hotel->name} ===");
        } else {
            $this->info('=== Cleaning Schedule Debug (ALL Hotels) ===');
        }

        $this->newLine();

        // Check bookings
        $bookingsQuery = Booking::with('room.hotel');
        if ($hotelId) {
            $bookingsQuery->whereHas('room', function ($q) use ($hotelId) {
                $q->where('hotel_id', $hotelId);
            });
        }
        $bookings = $bookingsQuery->get();

        $this->info("📋 Total Bookings: {$bookings->count()}");

        if ($bookings->count() > 0) {
            $this->info('   Recent bookings:');
            foreach ($bookings->take(5) as $booking) {
                $hotelName = $booking->room->hotel->name ?? 'Unknown';
                $this->info("   - {$booking->guest_name} in {$booking->room->room_number} @ {$hotelName} ({$booking->check_in} to {$booking->check_out})");
            }
        }
        $this->newLine();

        // Check cleaning tasks
        $tasksQuery = CleaningTask::with(['room.hotel', 'booking', 'cleaner.user']);
        if ($hotelId) {
            $tasksQuery->whereHas('room', function ($q) use ($hotelId) {
                $q->where('hotel_id', $hotelId);
            });
        }
        $tasks = $tasksQuery->get();

        $this->info("🧹 Total Cleaning Tasks: {$tasks->count()}");

        if ($tasks->count() > 0) {
            $this->info('   All tasks:');
            foreach ($tasks as $task) {
                $hotelName = $task->room->hotel->name ?? 'Unknown';
                $cleanerName = $task->cleaner?->user->name ?? 'Unassigned';
                $bookingInfo = $task->booking ? " (Booking: {$task->booking->guest_name})" : ' (No booking)';
                $this->info("   - Room {$task->room->room_number} @ {$hotelName} on {$task->date} - {$cleanerName} - Status: {$task->status}{$bookingInfo}");
            }
        } else {
            $this->warn('   ⚠️  No cleaning tasks found!');
        }
        $this->newLine();

        // Check tasks for today and future
        $futureTasksQuery = CleaningTask::where('date', '>=', today());
        if ($hotelId) {
            $futureTasksQuery->whereHas('room', function ($q) use ($hotelId) {
                $q->where('hotel_id', $hotelId);
            });
        }
        $futureTasks = $futureTasksQuery->count();
        $this->info("📅 Future/Today Tasks (date >= today): {$futureTasks}");
        $this->newLine();

        // Check by hotel
        $hotelsQuery = Hotel::with('rooms');
        if ($hotelId) {
            $hotelsQuery->where('id', $hotelId);
        }
        $hotels = $hotelsQuery->get();

        foreach ($hotels as $hotelItem) {
            $hotelTasks = CleaningTask::whereHas('room', function ($q) use ($hotelItem) {
                $q->where('hotel_id', $hotelItem->id);
            })->count();
            $this->info("🏨 {$hotelItem->name} (ID: {$hotelItem->id}): {$hotelTasks} tasks");
        }
        $this->newLine();

        // Check today's date
        $this->info("🗓️  Today's date: ".today()->toDateString());
        $this->info('🗓️  Today formatted: '.today()->format('Y-m-d'));

        return 0;
    }
}
