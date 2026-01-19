<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Cleaner;
use App\Models\CleaningTask;
use App\Models\DayCapacity;
use App\Models\Hotel;
use Illuminate\Console\Command;

class DiagnoseSystem extends Command
{
    protected $signature = 'housekeepr:diagnose {hotel_id?}';

    protected $description = 'Comprehensive system diagnostics';

    public function handle()
    {
        $hotelId = $this->argument('hotel_id');

        $this->info('=== SYSTEM DIAGNOSTICS ===');
        $this->info('Date: '.now()->format('Y-m-d H:i:s'));
        $this->newLine();

        // Hotels
        $this->info('--- HOTELS ---');
        $hotels = $hotelId ? Hotel::where('id', $hotelId)->get() : Hotel::all();
        foreach ($hotels as $hotel) {
            $this->info("Hotel #{$hotel->id}: {$hotel->name}");
        }
        $this->newLine();

        // Bookings
        $this->info('--- BOOKINGS ---');
        $bookingsQuery = Booking::query();
        if ($hotelId) {
            $bookingsQuery->whereHas('room', function ($q) use ($hotelId) {
                $q->where('hotel_id', $hotelId);
            });
        }
        $bookings = $bookingsQuery->with('room.hotel')->get();
        $this->info("Total bookings: {$bookings->count()}");

        foreach ($bookings->take(5) as $booking) {
            $room = $booking->room;
            $hotel = $room ? $room->hotel : null;

            $this->info("  Booking #{$booking->id}:");
            $this->info("    - Guest: {$booking->guest_name}");
            $this->info('    - Room: '.($room ? "#{$room->id} ({$room->room_number})" : 'MISSING ROOM!'));
            $this->info('    - Hotel: '.($hotel ? "#{$hotel->id} ({$hotel->name})" : 'MISSING HOTEL!'));
            $this->info("    - Check-in: {$booking->check_in_datetime}");
            $this->info('    - Has task: '.($booking->cleaningTask ? "Yes (#{$booking->cleaningTask->id})" : 'No'));
        }
        $this->newLine();

        // Cleaners
        $this->info('--- CLEANERS ---');
        $cleanersQuery = Cleaner::query();
        if ($hotelId) {
            $cleanersQuery->where('hotel_id', $hotelId);
        }
        $cleaners = $cleanersQuery->with('user')->get();
        $this->info("Total cleaners: {$cleaners->count()}");

        foreach ($cleaners as $cleaner) {
            $days = [];
            if ($cleaner->works_monday) {
                $days[] = 'Ma';
            }
            if ($cleaner->works_tuesday) {
                $days[] = 'Di';
            }
            if ($cleaner->works_wednesday) {
                $days[] = 'Wo';
            }
            if ($cleaner->works_thursday) {
                $days[] = 'Do';
            }
            if ($cleaner->works_friday) {
                $days[] = 'Vr';
            }
            if ($cleaner->works_saturday) {
                $days[] = 'Za';
            }
            if ($cleaner->works_sunday) {
                $days[] = 'Zo';
            }

            $this->info("  Cleaner #{$cleaner->id}: {$cleaner->user->name}");
            $this->info("    - Hotel: #{$cleaner->hotel_id}");
            $this->info("    - Status: {$cleaner->status}");
            $this->info('    - Works: '.implode(', ', $days));
        }
        $this->newLine();

        // Day Capacity
        $this->info('--- DAY CAPACITY ---');
        $capacityQuery = DayCapacity::query();
        if ($hotelId) {
            $capacityQuery->where('hotel_id', $hotelId);
        }
        $capacities = $capacityQuery->where('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->take(7)
            ->get();

        $this->info("Capacity records (next 7 days): {$capacities->count()}");
        foreach ($capacities as $capacity) {
            $this->info("  {$capacity->date} - Hotel #{$capacity->hotel_id}: {$capacity->capacity} cleaners");
        }
        $this->newLine();

        // Cleaning Tasks
        $this->info('--- CLEANING TASKS ---');
        $tasksQuery = CleaningTask::query();
        if ($hotelId) {
            $tasksQuery->whereHas('room', function ($q) use ($hotelId) {
                $q->where('hotel_id', $hotelId);
            });
        }
        $tasks = $tasksQuery->with('room.hotel', 'cleaner.user', 'booking')->get();
        $this->info("Total tasks: {$tasks->count()}");

        foreach ($tasks->take(5) as $task) {
            $this->info("  Task #{$task->id}:");
            $this->info("    - Date: {$task->date}");
            $this->info("    - Room: #{$task->room_id} (Hotel #{$task->room->hotel_id})");
            $this->info("    - Cleaner: {$task->cleaner->user->name}");
            $this->info("    - Status: {$task->status}");
        }
        $this->newLine();

        // Bookings without tasks
        $this->info('--- BOOKINGS WITHOUT TASKS ---');
        $bookingsWithoutTasks = $bookingsQuery->whereDoesntHave('cleaningTask')
            ->where('check_in_datetime', '>=', now())
            ->get();
        $this->info("Count: {$bookingsWithoutTasks->count()}");

        foreach ($bookingsWithoutTasks->take(5) as $booking) {
            $checkInDate = \Carbon\Carbon::parse($booking->check_in_datetime);
            $dayOfWeek = $checkInDate->dayOfWeek;
            $dayName = $checkInDate->locale('nl')->dayName;

            $this->info("  Booking #{$booking->id}: {$booking->guest_name}");
            $this->info("    - Check-in: {$booking->check_in_datetime} ({$dayName}, day {$dayOfWeek})");
            $this->info("    - Room: #{$booking->room_id}");
            $this->info("    - Hotel: #{$booking->room->hotel_id}");
        }

        $this->newLine();
        $this->info('=== END DIAGNOSTICS ===');

        return 0;
    }
}
