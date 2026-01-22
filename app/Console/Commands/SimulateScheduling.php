<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Cleaner;
use App\Models\CleaningTask;
use App\Models\Hotel;
use App\Models\Room;
use App\Services\CleaningScheduler;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SimulateScheduling extends Command
{
    protected $signature = 'housekeepr:simulate-scheduling {hotel_id?}';

    protected $description = 'Simulate various scheduling scenarios to test the cleaning scheduler algorithm';

    protected $hotel;

    protected $scheduler;

    protected $testDate;

    public function handle()
    {
        $this->info('🧪 HouseKeepr Scheduling Algorithm Simulation');
        $this->info('==========================================');
        $this->newLine();

        // Get hotel
        $hotelId = $this->argument('hotel_id') ?? Hotel::first()?->id;
        $this->hotel = Hotel::find($hotelId);

        if (! $this->hotel) {
            $this->error('No hotel found!');

            return 1;
        }

        $this->info("Testing with hotel: {$this->hotel->name} (ID: {$this->hotel->id})");
        $this->testDate = Carbon::tomorrow(); // Use tomorrow for testing

        $this->scheduler = new CleaningScheduler;

        // Run test scenarios
        $this->runScenario1_Normal();
        $this->runScenario2_Overlapping();
        $this->runScenario3_Impossible();
        $this->runScenario4_ManyRooms();
        $this->runScenario5_WorkloadBalancing();

        $this->newLine();
        $this->info('✅ All simulations complete!');

        return 0;
    }

    /**
     * Scenario 1: Normal scheduling (should work perfectly).
     */
    protected function runScenario1_Normal()
    {
        $this->newLine();
        $this->info('📋 SCENARIO 1: Normal Scheduling');
        $this->info('================================');
        $this->line('2 rooms, 2 cleaners, no conflicts');

        // Clean up
        $this->cleanup();

        // Create rooms
        $room1 = Room::first() ?? $this->createTestRoom('A1', 60);
        $room2 = Room::skip(1)->first() ?? $this->createTestRoom('A2', 60);

        // Create bookings (different times, no overlap)
        $booking1 = $this->createTestBooking($room1, '13:00'); // 11:00-12:10 (60+10+5)
        $booking2 = $this->createTestBooking($room2, '15:00'); // 11:00-12:10 (60+10+5)

        // Run scheduler
        $stats = $this->scheduler->scheduleForDate($this->hotel, $this->testDate);

        // Display results
        $this->displayResults($stats, [
            'Expected: All tasks scheduled',
            'Expected: No conflicts',
        ]);
    }

    /**
     * Scenario 2: Overlapping times (should assign different cleaners).
     */
    protected function runScenario2_Overlapping()
    {
        $this->newLine();
        $this->info('📋 SCENARIO 2: Overlapping Times');
        $this->info('=================================');
        $this->line('2 rooms, 2 cleaners, same check-in time');

        // Clean up
        $this->cleanup();

        // Create rooms
        $room1 = Room::first() ?? $this->createTestRoom('A1', 60);
        $room2 = Room::skip(1)->first() ?? $this->createTestRoom('A2', 90);

        // Create bookings (same time - should trigger different cleaner assignment)
        $booking1 = $this->createTestBooking($room1, '14:00'); // 11:00-12:15
        $booking2 = $this->createTestBooking($room2, '14:00'); // 11:00-12:45

        // Run scheduler
        $stats = $this->scheduler->scheduleForDate($this->hotel, $this->testDate);

        // Display results
        $this->displayResults($stats, [
            'Expected: Tasks assigned to different cleaners',
            'Expected: No conflicts if 2+ cleaners available',
        ]);
    }

    /**
     * Scenario 3: Impossible schedule (not enough time).
     */
    protected function runScenario3_Impossible()
    {
        $this->newLine();
        $this->info('📋 SCENARIO 3: Impossible Schedule');
        $this->info('==================================');
        $this->line('Room needs 120 min cleaning, but only 60 min available');

        // Clean up
        $this->cleanup();

        // Create room with 120 min cleaning time
        $room = Room::first() ?? $this->createTestRoom('B2', 120);

        // Create booking with only 60 min window (11:00 checkout, 12:00 check-in)
        $booking = $this->createTestBooking($room, '12:00');

        // Run scheduler
        $stats = $this->scheduler->scheduleForDate($this->hotel, $this->testDate);

        // Display results
        $this->displayResults($stats, [
            'Expected: Impossible schedule detected',
            'Expected: Specific issue created with time shortage details',
        ]);
    }

    /**
     * Scenario 4: Many rooms, testing capacity.
     */
    protected function runScenario4_ManyRooms()
    {
        $this->newLine();
        $this->info('📋 SCENARIO 4: Many Rooms');
        $this->info('==========================');
        $this->line('4 rooms, testing cleaner capacity');

        // Clean up
        $this->cleanup();

        // Create rooms
        $rooms = Room::take(4)->get();
        if ($rooms->count() < 4) {
            $this->warn('Not enough rooms for this scenario, skipping...');

            return;
        }

        // Create bookings for all rooms at same check-in time
        foreach ($rooms as $room) {
            $this->createTestBooking($room, '15:00');
        }

        // Run scheduler
        $stats = $this->scheduler->scheduleForDate($this->hotel, $this->testDate);

        // Display results
        $this->displayResults($stats, [
            'Expected: As many tasks as possible scheduled',
            'Expected: Conflicts if not enough cleaners',
        ]);
    }

    /**
     * Scenario 5: Workload balancing.
     */
    protected function runScenario5_WorkloadBalancing()
    {
        $this->newLine();
        $this->info('📋 SCENARIO 5: Workload Balancing');
        $this->info('==================================');
        $this->line('3 rooms, 2 cleaners, testing balanced distribution');

        // Clean up
        $this->cleanup();

        // Create rooms
        $rooms = Room::take(3)->get();
        if ($rooms->count() < 3) {
            $this->warn('Not enough rooms for this scenario, skipping...');

            return;
        }

        // Create bookings at different times
        $this->createTestBooking($rooms[0], '13:00');
        $this->createTestBooking($rooms[1], '14:00');
        $this->createTestBooking($rooms[2], '15:00');

        // Run scheduler
        $stats = $this->scheduler->scheduleForDate($this->hotel, $this->testDate);

        // Check task distribution
        $tasks = CleaningTask::whereDate('date', $this->testDate)->get();
        $distribution = $tasks->groupBy('cleaner_id')->map(fn ($group) => $group->count());

        // Display results
        $this->displayResults($stats, [
            'Expected: Tasks distributed evenly across cleaners',
            'Distribution: '.json_encode($distribution->toArray()),
        ]);
    }

    protected function displayResults(array $stats, array $expectations)
    {
        $this->newLine();
        $this->line('Results:');
        $this->line("  ✅ Scheduled: {$stats['scheduled']}");
        $this->line("  ⚠️  Conflicts: {$stats['conflicts']}");
        $this->line("  ❌ Impossible: {$stats['impossible']}");
        $this->line("  🚫 No Cleaner: {$stats['no_cleaner']}");

        $this->newLine();
        $this->line('Expectations:');
        foreach ($expectations as $expectation) {
            $this->line("  - {$expectation}");
        }

        // Show created tasks
        $tasks = CleaningTask::whereDate('date', $this->testDate)->with(['room', 'cleaner.user'])->get();
        if ($tasks->isNotEmpty()) {
            $this->newLine();
            $this->line('Created Tasks:');
            foreach ($tasks as $task) {
                $cleanerName = $task->cleaner?->user->name ?? 'Unassigned';
                $start = $task->suggested_start_time?->format('H:i') ?? '-';
                $deadline = $task->deadline?->format('H:i') ?? '-';
                $this->line("  • Room {$task->room->room_number}: {$start} - {$deadline} ({$task->planned_duration}min) → {$cleanerName}");
            }
        }
    }

    protected function createTestRoom(string $number, int $duration): Room
    {
        return Room::create([
            'hotel_id' => $this->hotel->id,
            'room_number' => $number,
            'room_type' => 'Test',
            'standard_duration' => $duration,
            'checkout_time' => '11:00',
            'checkin_time' => '15:00',
        ]);
    }

    protected function createTestBooking(Room $room, string $checkInTime): Booking
    {
        return Booking::create([
            'room_id' => $room->id,
            'guest_name' => 'Test Guest',
            'guest_email' => 'test@example.com',
            'check_in' => $this->testDate->format('Y-m-d'),
            'check_in_datetime' => $this->testDate->format('Y-m-d').' '.$checkInTime,
            'check_out' => $this->testDate->copy()->addDay()->format('Y-m-d'),
        ]);
    }

    protected function cleanup()
    {
        // Delete test bookings and tasks for tomorrow
        Booking::whereDate('check_in', $this->testDate)->delete();
        CleaningTask::whereDate('date', $this->testDate)->delete();
    }
}
