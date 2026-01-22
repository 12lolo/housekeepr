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

class SimulateRealisticSchedule extends Command
{
    protected $signature = 'housekeepr:simulate-realistic
                            {--date= : Specific date to test (format: Y-m-d, tests all dates if not provided)}
                            {--hotel= : Specific hotel ID to test (1 or 2, tests both if not provided)}
                            {--keep : Keep the simulated data after running (default: cleanup)}';

    protected $description = 'Simulate cleaning schedule with realistic test data';

    protected CleaningScheduler $scheduler;

    protected array $simulatedData = [
        'hotels' => [],
        'rooms' => [],
        'cleaners' => [],
        'bookings' => [],
        'users' => [],
    ];

    public function handle()
    {
        $this->scheduler = app(CleaningScheduler::class);

        $this->info('╔══════════════════════════════════════════════════════════════╗');
        $this->info('║   REALISTIC CLEANING SCHEDULER TEST                          ║');
        $this->info('╚══════════════════════════════════════════════════════════════╝');
        $this->newLine();

        // Load test data
        $this->info('📦 Loading realistic test data...');
        $this->loadTestData();
        $this->newLine();

        // Get filter options
        $specificDate = $this->option('date') ? Carbon::parse($this->option('date')) : null;
        $specificHotel = $this->option('hotel');

        // Get unique dates from bookings
        $dates = Booking::whereIn('id', $this->simulatedData['bookings'])
            ->selectRaw('DISTINCT DATE(check_in_datetime) as date')
            ->orderBy('date')
            ->pluck('date')
            ->map(fn ($d) => Carbon::parse($d));

        if ($specificDate) {
            $dates = $dates->filter(fn ($d) => $d->isSameDay($specificDate));
        }

        // Get hotels
        $hotels = Hotel::whereIn('id', $this->simulatedData['hotels'])->get();
        if ($specificHotel) {
            $hotels = $hotels->where('id', $specificHotel);
        }

        $this->info("🗓️  Testing {$dates->count()} date(s) across {$hotels->count()} hotel(s)");
        $this->newLine();

        // Test each date for each hotel
        foreach ($dates as $date) {
            foreach ($hotels as $hotel) {
                $this->testDateForHotel($hotel, $date);
            }
        }

        // Show summary
        $this->showSummary();

        // Cleanup unless --keep is specified
        if (! $this->option('keep')) {
            $this->cleanup();
            $this->info("\n✅ Simulation data cleaned up.");
        } else {
            $this->warn("\n⚠️  Simulation data kept in database");
        }

        return 0;
    }

    protected function loadTestData()
    {
        $testData = $this->getTestData();

        // Create hotels
        foreach ([1 => 'Hotel Amsterdam Central', 2 => 'Hotel Rotterdam Port'] as $id => $name) {
            $owner = User::create([
                'email' => "test-owner-{$id}-".uniqid().'@test.com',
                'password' => bcrypt('password'),
                'name' => "Test Owner {$id}",
                'role' => 'owner',
            ]);
            $this->simulatedData['users'][] = $owner->id;

            $hotel = Hotel::create([
                'name' => $name,
                'owner_id' => $owner->id,
            ]);
            $this->simulatedData['hotels'][] = $hotel->id;

            $this->line("   ✓ Created {$hotel->name}");
        }

        // Create rooms
        foreach ($testData['rooms'] as $roomData) {
            $room = Room::create([
                'hotel_id' => $this->simulatedData['hotels'][$roomData['hotel_id'] - 1],
                'room_number' => $roomData['room_number'],
                'room_type' => $roomData['room_type'],
                'standard_duration' => $roomData['standard_duration'],
                'checkout_time' => $roomData['checkout_time'],
                'checkin_time' => $roomData['checkin_time'],
            ]);
            $this->simulatedData['rooms'][$roomData['id']] = $room->id;
        }
        $this->line('   ✓ Created '.count($testData['rooms']).' rooms');

        // Create cleaners
        foreach ($testData['cleaners'] as $cleanerData) {
            if ($cleanerData['status'] === 'inactive') {
                continue; // Skip inactive cleaners
            }

            $user = User::create([
                'email' => "test-cleaner-{$cleanerData['id']}-".uniqid().'@test.com',
                'password' => bcrypt('password'),
                'name' => "Test Cleaner {$cleanerData['id']}",
                'role' => 'cleaner',
            ]);
            $this->simulatedData['users'][] = $user->id;

            $cleaner = Cleaner::create([
                'user_id' => $user->id,
                'hotel_id' => $this->simulatedData['hotels'][$cleanerData['hotel_id'] - 1],
                'status' => $cleanerData['status'],
                'works_monday' => $cleanerData['works_monday'],
                'works_tuesday' => $cleanerData['works_tuesday'],
                'works_wednesday' => $cleanerData['works_wednesday'],
                'works_thursday' => $cleanerData['works_thursday'],
                'works_friday' => $cleanerData['works_friday'],
                'works_saturday' => $cleanerData['works_saturday'],
                'works_sunday' => $cleanerData['works_sunday'],
            ]);
            $this->simulatedData['cleaners'][$cleanerData['id']] = $cleaner->id;
        }
        $this->line('   ✓ Created '.count($this->simulatedData['cleaners']).' active cleaners');

        // Create bookings (disable events to prevent auto-scheduling)
        foreach ($testData['bookings'] as $bookingData) {
            $booking = Event::fakeFor(function () use ($bookingData) {
                return Booking::create([
                    'room_id' => $this->simulatedData['rooms'][$bookingData['room_id']],
                    'guest_name' => $bookingData['guest_name'],
                    'check_in' => $bookingData['check_in'],
                    'check_in_datetime' => $bookingData['check_in_datetime'],
                    'check_out' => $bookingData['check_out'],
                    'check_out_datetime' => $bookingData['check_out_datetime'],
                    'notes' => $bookingData['notes'],
                ]);
            });
            $this->simulatedData['bookings'][] = $booking->id;
        }
        $this->line('   ✓ Created '.count($testData['bookings']).' bookings');
    }

    protected function testDateForHotel(Hotel $hotel, Carbon $date)
    {
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info("📅 {$date->format('l, F j, Y')} - {$hotel->name}");
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        // Get bookings for this date
        $bookings = Booking::whereIn('bookings.id', $this->simulatedData['bookings'])
            ->whereDate('check_in_datetime', $date)
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->where('rooms.hotel_id', $hotel->id)
            ->select('bookings.*')
            ->get();

        if ($bookings->isEmpty()) {
            $this->line('   No check-ins on this date');
            $this->newLine();

            return;
        }

        $this->info("\n📋 Check-ins on this date:");
        foreach ($bookings as $booking) {
            $room = $booking->room;
            $checkInTime = Carbon::parse($booking->check_in_datetime)->format('H:i');
            $this->line("   • Room {$room->room_number} - {$booking->guest_name} (check-in: {$checkInTime})");
        }

        // Get available cleaners
        $dayOfWeek = $date->dayOfWeek;
        $dayColumn = $this->getDayColumn($dayOfWeek);
        $availableCleaners = Cleaner::whereIn('id', array_values($this->simulatedData['cleaners']))
            ->where('hotel_id', $hotel->id)
            ->where('status', 'active')
            ->where($dayColumn, true)
            ->with('user')
            ->get();

        $this->info("\n👥 Available cleaners: {$availableCleaners->count()}");
        foreach ($availableCleaners as $cleaner) {
            $this->line("   • {$cleaner->user->name}");
        }

        // Run scheduler
        $this->info("\n⚙️  Running Scheduler...");
        $tasksBefore = DB::table('cleaning_tasks')->count();
        $issuesBefore = Issue::count();

        $stats = $this->scheduler->scheduleForDate($hotel, $date);

        $tasksAfter = DB::table('cleaning_tasks')->count();
        $issuesAfter = Issue::count();

        // Show results
        $this->newLine();
        $this->info('📈 RESULTS:');
        $this->info("   ✅ Scheduled: {$stats['scheduled']}");
        $this->info("   ⚠️  Conflicts: {$stats['conflicts']}");
        $this->info("   ❌ No Cleaner: {$stats['no_cleaner']}");
        $this->info("   🚫 Impossible: {$stats['impossible']}");

        // Show created tasks
        if ($stats['scheduled'] > 0) {
            $this->newLine();
            $this->info('📋 Created Cleaning Tasks:');
            $tasks = DB::table('cleaning_tasks')
                ->join('rooms', 'cleaning_tasks.room_id', '=', 'rooms.id')
                ->join('cleaners', 'cleaning_tasks.cleaner_id', '=', 'cleaners.id')
                ->join('users', 'cleaners.user_id', '=', 'users.id')
                ->whereIn('cleaning_tasks.booking_id', $this->simulatedData['bookings'])
                ->where('cleaning_tasks.date', $date->toDateString())
                ->where('rooms.hotel_id', $hotel->id)
                ->select(
                    'rooms.room_number',
                    'users.name as cleaner_name',
                    'cleaning_tasks.suggested_start_time',
                    'cleaning_tasks.planned_duration',
                    'cleaning_tasks.deadline'
                )
                ->orderBy('cleaning_tasks.suggested_start_time')
                ->get();

            foreach ($tasks as $task) {
                $start = Carbon::parse($task->suggested_start_time)->format('H:i');
                $end = Carbon::parse($task->suggested_start_time)->addMinutes($task->planned_duration + 5)->format('H:i');
                $deadline = Carbon::parse($task->deadline)->format('H:i');
                $this->line("   • Room {$task->room_number} → {$task->cleaner_name} ({$start}-{$end}, deadline: {$deadline})");
            }
        }

        // Show issues
        if ($stats['conflicts'] > 0 || $stats['impossible'] > 0 || $stats['no_cleaner'] > 0) {
            $this->newLine();
            $this->warn('⚠️  Issues Created:');
            $issues = Issue::whereIn('room_id', array_values($this->simulatedData['rooms']))
                ->where('status', 'open')
                ->where('created_at', '>', now()->subMinute())
                ->with('room')
                ->get();

            foreach ($issues as $issue) {
                $noteLines = explode("\n", $issue->note);
                $this->line("   • Room {$issue->room->room_number}: {$noteLines[0]}");
            }
        }

        $this->newLine();
    }

    protected function showSummary()
    {
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('📊 OVERALL SUMMARY');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        $totalTasks = DB::table('cleaning_tasks')
            ->whereIn('booking_id', $this->simulatedData['bookings'])
            ->count();

        $totalIssues = Issue::whereIn('room_id', array_values($this->simulatedData['rooms']))
            ->where('status', 'open')
            ->count();

        $this->newLine();
        $this->info("✅ Total Cleaning Tasks Created: {$totalTasks}");
        $this->warn("⚠️  Total Issues Created: {$totalIssues}");

        if ($totalIssues > 0) {
            $this->newLine();
            $this->info('Issue Breakdown:');
            $issues = Issue::whereIn('room_id', array_values($this->simulatedData['rooms']))
                ->where('status', 'open')
                ->get();

            $impossible = $issues->filter(fn ($i) => str_contains($i->note, 'Onvoldoende tijd'))->count();
            $conflicts = $issues->filter(fn ($i) => str_contains($i->note, 'Alle schoonmakers zijn bezet'))->count();
            $noCleaner = $issues->filter(fn ($i) => str_contains($i->note, 'Geen schoonmakers beschikbaar'))->count();

            $this->line("   🚫 Impossible schedules: {$impossible}");
            $this->line("   ⚠️  Scheduling conflicts: {$conflicts}");
            $this->line("   ❌ No cleaners available: {$noCleaner}");
        }

        $this->newLine();
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

    protected function cleanup()
    {
        if (! empty($this->simulatedData['bookings'])) {
            DB::table('cleaning_tasks')->whereIn('booking_id', $this->simulatedData['bookings'])->delete();
            Booking::whereIn('id', $this->simulatedData['bookings'])->delete();
        }
        if (! empty($this->simulatedData['rooms'])) {
            Issue::whereIn('room_id', array_values($this->simulatedData['rooms']))->delete();
            Room::whereIn('id', array_values($this->simulatedData['rooms']))->delete();
        }
        if (! empty($this->simulatedData['cleaners'])) {
            Cleaner::whereIn('id', array_values($this->simulatedData['cleaners']))->delete();
        }
        if (! empty($this->simulatedData['hotels'])) {
            Hotel::whereIn('id', $this->simulatedData['hotels'])->delete();
        }
        if (! empty($this->simulatedData['users'])) {
            User::whereIn('id', $this->simulatedData['users'])->delete();
        }
    }

    protected function getTestData(): array
    {
        return [
            'rooms' => [
                ['id' => 1, 'hotel_id' => 1, 'room_number' => '101', 'room_type' => 'Standard', 'standard_duration' => 30, 'checkout_time' => '11:00', 'checkin_time' => '15:00'],
                ['id' => 2, 'hotel_id' => 1, 'room_number' => '102', 'room_type' => 'Standard', 'standard_duration' => 30, 'checkout_time' => '11:00', 'checkin_time' => '15:00'],
                ['id' => 3, 'hotel_id' => 1, 'room_number' => '103', 'room_type' => 'Deluxe', 'standard_duration' => 45, 'checkout_time' => '11:00', 'checkin_time' => '15:00'],
                ['id' => 4, 'hotel_id' => 1, 'room_number' => '201', 'room_type' => 'Suite', 'standard_duration' => 60, 'checkout_time' => '11:00', 'checkin_time' => '15:00'],
                ['id' => 5, 'hotel_id' => 1, 'room_number' => '202', 'room_type' => 'Standard', 'standard_duration' => 35, 'checkout_time' => '12:00', 'checkin_time' => '16:00'],
                ['id' => 6, 'hotel_id' => 2, 'room_number' => 'A1', 'room_type' => 'Standard', 'standard_duration' => 25, 'checkout_time' => '10:30', 'checkin_time' => '14:30'],
                ['id' => 7, 'hotel_id' => 2, 'room_number' => 'A2', 'room_type' => 'Deluxe', 'standard_duration' => 40, 'checkout_time' => '10:30', 'checkin_time' => '14:30'],
                ['id' => 8, 'hotel_id' => 2, 'room_number' => 'B1', 'room_type' => 'Suite', 'standard_duration' => 70, 'checkout_time' => '11:00', 'checkin_time' => '15:00'],
            ],
            'cleaners' => [
                ['id' => 1, 'user_id' => 101, 'hotel_id' => 1, 'status' => 'active', 'works_monday' => 1, 'works_tuesday' => 1, 'works_wednesday' => 1, 'works_thursday' => 1, 'works_friday' => 1, 'works_saturday' => 0, 'works_sunday' => 0],
                ['id' => 2, 'user_id' => 102, 'hotel_id' => 1, 'status' => 'active', 'works_monday' => 0, 'works_tuesday' => 1, 'works_wednesday' => 1, 'works_thursday' => 1, 'works_friday' => 1, 'works_saturday' => 1, 'works_sunday' => 0],
                ['id' => 3, 'user_id' => 103, 'hotel_id' => 1, 'status' => 'active', 'works_monday' => 1, 'works_tuesday' => 1, 'works_wednesday' => 1, 'works_thursday' => 1, 'works_friday' => 1, 'works_saturday' => 1, 'works_sunday' => 1],
                ['id' => 4, 'user_id' => 104, 'hotel_id' => 1, 'status' => 'inactive', 'works_monday' => 1, 'works_tuesday' => 1, 'works_wednesday' => 1, 'works_thursday' => 1, 'works_friday' => 1, 'works_saturday' => 0, 'works_sunday' => 0],
                ['id' => 5, 'user_id' => 201, 'hotel_id' => 2, 'status' => 'active', 'works_monday' => 1, 'works_tuesday' => 1, 'works_wednesday' => 1, 'works_thursday' => 1, 'works_friday' => 1, 'works_saturday' => 0, 'works_sunday' => 0],
                ['id' => 6, 'user_id' => 202, 'hotel_id' => 2, 'status' => 'active', 'works_monday' => 0, 'works_tuesday' => 0, 'works_wednesday' => 1, 'works_thursday' => 1, 'works_friday' => 1, 'works_saturday' => 1, 'works_sunday' => 1],
            ],
            'bookings' => [
                ['id' => 1, 'room_id' => 1, 'guest_name' => 'Eva de Vries', 'check_in' => '2026-02-01', 'check_in_datetime' => '2026-02-01 15:05:00', 'check_out' => '2026-02-03', 'check_out_datetime' => '2026-02-03 10:45:00', 'notes' => 'Late arrival, extra towels'],
                ['id' => 2, 'room_id' => 2, 'guest_name' => 'Noah Jansen', 'check_in' => '2026-02-02', 'check_in_datetime' => '2026-02-02 15:10:00', 'check_out' => '2026-02-04', 'check_out_datetime' => '2026-02-04 10:55:00', 'notes' => null],
                ['id' => 3, 'room_id' => 2, 'guest_name' => 'Sophie Bakker', 'check_in' => '2026-02-04', 'check_in_datetime' => '2026-02-04 15:00:00', 'check_out' => '2026-02-06', 'check_out_datetime' => null, 'notes' => 'Check-out time unknown yet (still staying?)'],
                ['id' => 4, 'room_id' => 3, 'guest_name' => 'Liam Visser', 'check_in' => '2026-02-01', 'check_in_datetime' => '2026-02-01 15:30:00', 'check_out' => '2026-02-07', 'check_out_datetime' => '2026-02-07 11:00:00', 'notes' => 'VIP guest'],
                ['id' => 5, 'room_id' => 4, 'guest_name' => 'Mila Smit', 'check_in' => '2026-02-03', 'check_in_datetime' => '2026-02-03 16:15:00', 'check_out' => '2026-02-05', 'check_out_datetime' => '2026-02-05 12:20:00', 'notes' => 'Late checkout approved'],
                ['id' => 6, 'room_id' => 5, 'guest_name' => 'Daan Postma', 'check_in' => '2026-02-04', 'check_in_datetime' => '2026-02-04 16:05:00', 'check_out' => '2026-02-06', 'check_out_datetime' => '2026-02-06 11:50:00', 'notes' => 'Room 202 has later times'],
                ['id' => 7, 'room_id' => 1, 'guest_name' => 'Julia van Leeuwen', 'check_in' => '2026-02-10', 'check_in_datetime' => '2026-02-10 13:45:00', 'check_out' => '2026-02-12', 'check_out_datetime' => null, 'notes' => 'Early check-in, checkout unknown'],
                ['id' => 8, 'room_id' => 3, 'guest_name' => 'Finn Kuiper', 'check_in' => '2026-02-08', 'check_in_datetime' => '2026-02-08 15:00:00', 'check_out' => '2026-02-09', 'check_out_datetime' => '2026-02-09 09:10:00', 'notes' => 'Short stay'],
                ['id' => 9, 'room_id' => 6, 'guest_name' => 'Sara Meijer', 'check_in' => '2026-02-01', 'check_in_datetime' => '2026-02-01 14:30:00', 'check_out' => '2026-02-03', 'check_out_datetime' => '2026-02-03 10:20:00', 'notes' => null],
                ['id' => 10, 'room_id' => 7, 'guest_name' => 'Lucas van Dijk', 'check_in' => '2026-02-02', 'check_in_datetime' => '2026-02-02 14:45:00', 'check_out' => '2026-02-04', 'check_out_datetime' => '2026-02-04 10:25:00', 'notes' => 'Extra pillows requested'],
                ['id' => 11, 'room_id' => 8, 'guest_name' => 'Anna Willems', 'check_in' => '2026-02-04', 'check_in_datetime' => '2026-02-04 15:20:00', 'check_out' => '2026-02-05', 'check_out_datetime' => '2026-02-05 10:55:00', 'notes' => 'High cleaning duration room'],
                ['id' => 12, 'room_id' => 8, 'guest_name' => 'Thomas van der Meer', 'check_in' => '2026-02-05', 'check_in_datetime' => '2026-02-05 14:50:00', 'check_out' => '2026-02-06', 'check_out_datetime' => '2026-02-06 10:40:00', 'notes' => 'Back-to-back suite booking'],
                ['id' => 13, 'room_id' => 7, 'guest_name' => 'Overlap Guest A', 'check_in' => '2026-02-06', 'check_in_datetime' => '2026-02-06 14:30:00', 'check_out' => '2026-02-08', 'check_out_datetime' => '2026-02-08 10:20:00', 'notes' => 'Intentional overlap test'],
                ['id' => 14, 'room_id' => 7, 'guest_name' => 'Overlap Guest B', 'check_in' => '2026-02-07', 'check_in_datetime' => '2026-02-07 14:30:00', 'check_out' => '2026-02-09', 'check_out_datetime' => '2026-02-09 10:30:00', 'notes' => 'Intentional overlap test #2'],
                ['id' => 15, 'room_id' => 4, 'guest_name' => 'Heavy Clean Test', 'check_in' => '2026-02-11', 'check_in_datetime' => '2026-02-11 15:00:00', 'check_out' => '2026-02-12', 'check_out_datetime' => '2026-02-12 10:40:00', 'notes' => 'Pet hair + extra deep clean required'],
            ],
        ];
    }
}
