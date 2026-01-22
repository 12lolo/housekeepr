<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Cleaner;
use App\Models\Hotel;
use App\Models\Issue;
use App\Models\Room;
use App\Models\User;
use App\Services\CleaningScheduler;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class SimulateCleaningSchedule extends Command
{
    protected $signature = 'housekeepr:simulate-schedule
                            {--date= : Date to simulate (format: Y-m-d, defaults to tomorrow)}
                            {--scenario= : Specific scenario to test (normal|tight|impossible|conflicts|no-cleaners|all)}
                            {--keep : Keep the simulated data after running (default: cleanup)}';

    protected $description = 'Simulate cleaning schedule scenarios to test the algorithm';

    protected CleaningScheduler $scheduler;

    protected Hotel $hotel;

    protected Carbon $simulationDate;

    protected array $simulatedData = [
        'hotel' => null,
        'owner' => null,
        'rooms' => [],
        'cleaners' => [],
        'bookings' => [],
        'users' => [],
    ];

    public function handle()
    {
        $this->scheduler = app(CleaningScheduler::class);

        // Create temporary owner for simulation hotel
        $tempOwner = User::create([
            'email' => 'sim-owner-'.uniqid().'@housekeepr.test',
            'password' => bcrypt('password'),
            'name' => 'Simulation Owner',
            'role' => 'owner',
        ]);
        $this->simulatedData['owner'] = $tempOwner->id;

        // Create temporary hotel for simulation
        $this->hotel = Hotel::create([
            'name' => 'SIMULATION Hotel (Temp)',
            'owner_id' => $tempOwner->id,
        ]);
        $this->simulatedData['hotel'] = $this->hotel->id;

        // Get simulation date
        $dateInput = $this->option('date');
        $this->simulationDate = $dateInput ? Carbon::parse($dateInput) : Carbon::tomorrow();

        $this->info('╔══════════════════════════════════════════════════════════════╗');
        $this->info('║   CLEANING SCHEDULER SIMULATION                              ║');
        $this->info('╚══════════════════════════════════════════════════════════════╝');
        $this->newLine();
        $this->info("🏨 Hotel: {$this->hotel->name} (ID: {$this->hotel->id})");
        $this->info("📅 Simulation Date: {$this->simulationDate->format('l, Y-m-d')} (Day: {$this->simulationDate->dayOfWeek})");
        $this->newLine();

        // Get scenario
        $scenario = $this->option('scenario') ?: 'all';

        if ($scenario === 'all') {
            $this->runAllScenarios();
        } else {
            $this->runScenario($scenario);
        }

        // Cleanup unless --keep is specified
        if (! $this->option('keep')) {
            $this->cleanup();
            $this->info("\n✅ Simulation data cleaned up.");
        } else {
            $this->warn("\n⚠️  Simulation data kept in database (use --keep flag to preserve)");
        }

        return 0;
    }

    protected function runAllScenarios()
    {
        $scenarios = ['normal', 'tight', 'impossible', 'conflicts', 'no-cleaners'];

        foreach ($scenarios as $scenario) {
            $this->runScenario($scenario);
            $this->newLine(2);

            // Cleanup between scenarios
            $this->cleanupPartial();
        }
    }

    protected function runScenario(string $scenario)
    {
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        switch ($scenario) {
            case 'normal':
                $this->info('📊 SCENARIO 1: Normal Schedule (Enough time, available cleaners)');
                $this->simulateNormalSchedule();
                break;

            case 'tight':
                $this->info('📊 SCENARIO 2: Tight Schedule (Just enough time)');
                $this->simulateTightSchedule();
                break;

            case 'impossible':
                $this->info('📊 SCENARIO 3: Impossible Schedule (Not enough time)');
                $this->simulateImpossibleSchedule();
                break;

            case 'conflicts':
                $this->info('📊 SCENARIO 4: Scheduling Conflicts (All cleaners busy)');
                $this->simulateConflicts();
                break;

            case 'no-cleaners':
                $this->info('📊 SCENARIO 5: No Cleaners Available');
                $this->simulateNoCleaners();
                break;

            default:
                $this->error("Unknown scenario: {$scenario}");

                return;
        }

        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }

    protected function simulateNormalSchedule()
    {
        $this->info("\n📝 Setup:");
        $this->info('   • 2 cleaners available');
        $this->info('   • 3 rooms with normal checkout→checkin times');
        $this->info('   • Standard 60-minute cleaning duration');
        $this->newLine();

        // Create cleaners
        $cleaners = $this->createCleaners(2);

        // Create rooms with normal times
        $rooms = [
            $this->createRoom('SIM-101', 'Standard', 60, '11:00', '15:00'), // 4 hours gap
            $this->createRoom('SIM-102', 'Deluxe', 60, '10:00', '14:00'),   // 4 hours gap
            $this->createRoom('SIM-103', 'Suite', 60, '12:00', '16:00'),    // 4 hours gap
        ];

        // Create bookings for each room
        foreach ($rooms as $room) {
            $this->createBooking($room);
        }

        $this->runSchedulerAndShowResults();
    }

    protected function simulateTightSchedule()
    {
        $this->info("\n📝 Setup:");
        $this->info('   • 2 cleaners available');
        $this->info('   • 2 rooms with TIGHT checkout→checkin times');
        $this->info('   • 60-minute cleaning + 10 min buffer + 5 min travel = 75 min needed');
        $this->info('   • Only 90 minutes available (tight but possible)');
        $this->newLine();

        $cleaners = $this->createCleaners(2);

        // Tight schedule: 10:00 checkout → 11:30 checkin (90 minutes)
        $rooms = [
            $this->createRoom('SIM-201', 'Standard', 60, '10:00', '11:30'),
            $this->createRoom('SIM-202', 'Deluxe', 60, '11:00', '12:30'),
        ];

        foreach ($rooms as $room) {
            $this->createBooking($room);
        }

        $this->runSchedulerAndShowResults();
    }

    protected function simulateImpossibleSchedule()
    {
        $this->info("\n📝 Setup:");
        $this->info('   • 2 cleaners available');
        $this->info('   • 2 rooms with IMPOSSIBLE checkout→checkin times');
        $this->info('   • 60-minute cleaning + 10 min buffer + 5 min travel = 75 min needed');
        $this->info('   • Only 60 minutes available (IMPOSSIBLE!)');
        $this->newLine();

        $cleaners = $this->createCleaners(2);

        // Impossible: 10:00 checkout → 11:00 checkin (60 minutes, but need 75)
        $rooms = [
            $this->createRoom('SIM-301', 'Standard', 60, '10:00', '11:00'),
            $this->createRoom('SIM-302', 'Deluxe', 60, '11:00', '12:00'),
        ];

        foreach ($rooms as $room) {
            $this->createBooking($room);
        }

        $this->runSchedulerAndShowResults();
    }

    protected function simulateConflicts()
    {
        $this->info("\n📝 Setup:");
        $this->info('   • Only 1 cleaner available');
        $this->info('   • 3 rooms ALL checking out at 10:00 and checking in at 14:00');
        $this->info('   • Each needs 75 minutes → total 225 min needed');
        $this->info("   • Only 240 min available, but ONE cleaner can't do all 3!");
        $this->newLine();

        // Only 1 cleaner!
        $cleaners = $this->createCleaners(1);

        // All rooms have same times - conflicts!
        $rooms = [
            $this->createRoom('SIM-401', 'Standard', 60, '10:00', '14:00'),
            $this->createRoom('SIM-402', 'Deluxe', 60, '10:00', '14:00'),
            $this->createRoom('SIM-403', 'Suite', 60, '10:00', '14:00'),
        ];

        foreach ($rooms as $room) {
            $this->createBooking($room);
        }

        $this->runSchedulerAndShowResults();
    }

    protected function simulateNoCleaners()
    {
        $this->info("\n📝 Setup:");
        $this->info('   • NO cleaners available on this day');
        $this->info('   • 2 rooms need cleaning');
        $this->newLine();

        // Don't create any cleaners!

        $rooms = [
            $this->createRoom('SIM-501', 'Standard', 60, '10:00', '15:00'),
            $this->createRoom('SIM-502', 'Deluxe', 60, '11:00', '16:00'),
        ];

        foreach ($rooms as $room) {
            $this->createBooking($room);
        }

        $this->runSchedulerAndShowResults();
    }

    protected function createCleaners(int $count): array
    {
        $cleaners = [];
        $dayColumn = $this->getDayColumn($this->simulationDate->dayOfWeek);

        for ($i = 1; $i <= $count; $i++) {
            $user = User::create([
                'email' => "sim-cleaner-{$i}-".uniqid().'@housekeepr.test',
                'password' => bcrypt('password'),
                'name' => "Sim Cleaner {$i}",
                'role' => 'cleaner',
            ]);

            $cleaner = Cleaner::create([
                'user_id' => $user->id,
                'hotel_id' => $this->hotel->id,
                'status' => 'active',
                'works_monday' => true,
                'works_tuesday' => true,
                'works_wednesday' => true,
                'works_thursday' => true,
                'works_friday' => true,
                'works_saturday' => true,
                'works_sunday' => true,
            ]);

            $cleaners[] = $cleaner;
            $this->simulatedData['cleaners'][] = $cleaner->id;
            $this->simulatedData['users'][] = $user->id;
        }

        $this->line("   ✓ Created {$count} cleaner(s)");

        return $cleaners;
    }

    protected function createRoom(string $roomNumber, string $type, int $duration, string $checkoutTime, string $checkinTime): Room
    {
        $room = Room::create([
            'hotel_id' => $this->hotel->id,
            'room_number' => $roomNumber,
            'room_type' => $type,
            'standard_duration' => $duration,
            'checkout_time' => $checkoutTime,
            'checkin_time' => $checkinTime,
        ]);

        $this->simulatedData['rooms'][] = $room->id;

        return $room;
    }

    protected function createBooking(Room $room): Booking
    {
        $checkoutTime = Carbon::parse($this->simulationDate->toDateString().' '.$room->checkout_time);
        $checkinTime = Carbon::parse($this->simulationDate->toDateString().' '.$room->checkin_time);

        // Disable events to prevent automatic scheduler execution
        $booking = Event::fakeFor(function () use ($room, $checkinTime) {
            return Booking::create([
                'room_id' => $room->id,
                'guest_name' => 'Simulation Guest '.$room->room_number,
                'check_in' => $this->simulationDate->toDateString(),
                'check_out' => $this->simulationDate->copy()->addDay()->toDateString(),
                'check_in_datetime' => $checkinTime,
                'check_out_datetime' => $this->simulationDate->copy()->addDay()->setTimeFromTimeString($room->checkout_time),
            ]);
        });

        $this->simulatedData['bookings'][] = $booking->id;

        return $booking;
    }

    protected function runSchedulerAndShowResults()
    {
        $this->info('⚙️  Running Scheduler...');
        $this->newLine();

        // Count before
        $tasksBefore = DB::table('cleaning_tasks')->count();
        $issuesBefore = Issue::count();

        // Run scheduler
        $stats = $this->scheduler->scheduleForDate($this->hotel, $this->simulationDate);

        // Count after
        $tasksAfter = DB::table('cleaning_tasks')->count();
        $issuesAfter = Issue::count();

        // Show stats
        $this->info('📈 RESULTS:');
        $this->info("   ✅ Scheduled: {$stats['scheduled']}");
        $this->info("   ⚠️  Conflicts: {$stats['conflicts']}");
        $this->info("   ❌ No Cleaner: {$stats['no_cleaner']}");
        $this->info("   🚫 Impossible: {$stats['impossible']}");
        $this->newLine();

        // Show created tasks
        if (! empty($this->simulatedData['bookings'])) {
            $tasks = DB::table('cleaning_tasks')
                ->join('rooms', 'cleaning_tasks.room_id', '=', 'rooms.id')
                ->leftJoin('cleaners', 'cleaning_tasks.cleaner_id', '=', 'cleaners.id')
                ->leftJoin('users', 'cleaners.user_id', '=', 'users.id')
                ->whereIn('cleaning_tasks.booking_id', $this->simulatedData['bookings'])
                ->select('rooms.room_number', 'users.name as cleaner_name',
                    'cleaning_tasks.suggested_start_time', 'cleaning_tasks.planned_duration',
                    'cleaning_tasks.deadline', 'cleaning_tasks.status')
                ->orderBy('cleaning_tasks.suggested_start_time')
                ->get();

            if ($tasks->count() > 0) {
                $this->info("📋 Created Tasks: {$tasks->count()}");
                foreach ($tasks as $task) {
                    $start = Carbon::parse($task->suggested_start_time)->format('H:i');
                    $end = Carbon::parse($task->suggested_start_time)->addMinutes($task->planned_duration)->format('H:i');
                    $deadline = Carbon::parse($task->deadline)->format('H:i');
                    $cleaner = $task->cleaner_name ?? 'Unassigned';
                    $this->line("   • Room {$task->room_number} → {$cleaner} ({$start}-{$end}, deadline: {$deadline}) [{$task->status}]");
                }
                $this->newLine();
            }
        }

        // Show created issues
        if (! empty($this->simulatedData['rooms'])) {
            $issues = Issue::whereIn('room_id', $this->simulatedData['rooms'])
                ->where('status', 'open')
                ->orderBy('created_at', 'desc')
                ->get();

            if ($issues->count() > 0) {
                $this->warn("⚠️  Created Issues: {$issues->count()}");
                foreach ($issues as $issue) {
                    $room = Room::find($issue->room_id);
                    $this->line("   • Room {$room->room_number}: {$issue->impact}");
                    $noteLines = explode("\n", $issue->note);
                    $this->line('     '.$noteLines[0]);
                }
                $this->newLine();
            }
        }

        // Show interpretation
        $this->info('💡 INTERPRETATION:');
        if ($stats['scheduled'] > 0 && $stats['conflicts'] === 0 && $stats['impossible'] === 0 && $stats['no_cleaner'] === 0) {
            $this->info('   ✅ Perfect! All tasks scheduled successfully.');
        } elseif ($stats['impossible'] > 0) {
            $this->warn('   ⚠️  Some schedules are physically impossible (not enough time).');
            $this->warn('   → Solution: Adjust checkout/checkin times or reduce cleaning duration.');
        } elseif ($stats['no_cleaner'] > 0) {
            $this->warn('   ⚠️  No cleaners available on this day.');
            $this->warn('   → Solution: Add cleaners or adjust their availability.');
        } elseif ($stats['conflicts'] > 0) {
            $this->warn('   ⚠️  Scheduling conflicts - all cleaners are busy at required times.');
            $this->warn('   → Solution: Add more cleaners or spread out booking times.');
        }
    }

    protected function getDayColumn(int $dayOfWeek): string
    {
        return match ($dayOfWeek) {
            0 => 'works_sunday',
            1 => 'works_monday',
            2 => 'works_tuesday',
            3 => 'works_wednesday',
            4 => 'works_thursday',
            5 => 'works_friday',
            6 => 'works_saturday',
        };
    }

    protected function cleanupPartial()
    {
        // Cleanup for next scenario (but keep hotel and owner)
        if (! empty($this->simulatedData['bookings'])) {
            DB::table('cleaning_tasks')->whereIn('booking_id', $this->simulatedData['bookings'])->delete();
            Booking::whereIn('id', $this->simulatedData['bookings'])->delete();
        }
        if (! empty($this->simulatedData['rooms'])) {
            Issue::whereIn('room_id', $this->simulatedData['rooms'])->delete();
            Room::whereIn('id', $this->simulatedData['rooms'])->delete();
        }
        if (! empty($this->simulatedData['cleaners'])) {
            Cleaner::whereIn('id', $this->simulatedData['cleaners'])->delete();
        }
        if (! empty($this->simulatedData['users'])) {
            User::whereIn('id', $this->simulatedData['users'])->delete();
        }

        // Reset (keep hotel and owner)
        $hotelId = $this->simulatedData['hotel'];
        $ownerId = $this->simulatedData['owner'];
        $this->simulatedData = [
            'hotel' => $hotelId,
            'owner' => $ownerId,
            'rooms' => [],
            'cleaners' => [],
            'bookings' => [],
            'users' => [],
        ];
    }

    protected function cleanup()
    {
        $this->cleanupPartial();

        // Delete the temporary hotel and owner
        if ($this->simulatedData['hotel']) {
            Hotel::where('id', $this->simulatedData['hotel'])->delete();
        }
        if ($this->simulatedData['owner']) {
            User::where('id', $this->simulatedData['owner'])->delete();
        }
    }
}
