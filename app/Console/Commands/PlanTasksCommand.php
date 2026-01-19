<?php

namespace App\Console\Commands;

use App\Mail\UrgentIssueMail;
use App\Models\Booking;
use App\Models\Cleaner;
use App\Models\CleaningTask;
use App\Models\DayCapacity;
use App\Models\Hotel;
use App\Models\Issue;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PlanTasksCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hcs:plan-tasks
                            {--hotel= : Specific hotel ID to plan for}
                            {--date= : Specific date to plan for (Y-m-d)}
                            {--days=7 : Number of days ahead to plan}
                            {--force : Force replanning of existing tasks}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Plan or replan cleaning tasks based on bookings, capacity, and cleaner availability (UC-S1, UC-S2)';

    protected $stats = [
        'planned' => 0,
        'replanned' => 0,
        'blocked' => 0,
        'errors' => 0,
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧹 HouseKeepr Task Planner');
        $this->info('========================');

        $hotelId = $this->option('hotel');
        $specificDate = $this->option('date');
        $daysAhead = (int) $this->option('days');
        $force = $this->option('force');

        // Determine date range
        $startDate = $specificDate ? Carbon::parse($specificDate) : today();
        $endDate = $startDate->copy()->addDays($daysAhead);

        $this->info("Planning period: {$startDate->format('d-m-Y')} to {$endDate->format('d-m-Y')}");

        // Get hotels to process
        $hotels = $hotelId
            ? Hotel::where('id', $hotelId)->get()
            : Hotel::with('owner')->get();

        if ($hotels->isEmpty()) {
            $this->error('No hotels found to plan for.');

            return 1;
        }

        $this->info("Processing {$hotels->count()} hotel(s)...\n");

        foreach ($hotels as $hotel) {
            $this->processHotel($hotel, $startDate, $endDate, $force);
        }

        // Display statistics
        $this->newLine();
        $this->info('📊 Planning Statistics');
        $this->info('======================');
        $this->line("✅ Tasks planned: {$this->stats['planned']}");
        $this->line("🔄 Tasks replanned: {$this->stats['replanned']}");
        $this->line("🚫 Tasks blocked: {$this->stats['blocked']}");
        $this->line("❌ Errors: {$this->stats['errors']}");

        return 0;
    }

    protected function processHotel(Hotel $hotel, Carbon $startDate, Carbon $endDate, bool $force)
    {
        $this->info("🏨 Processing: {$hotel->name} (ID: {$hotel->id})");

        // Get active cleaners
        $cleaners = $hotel->cleaners()->where('status', 'active')->get();

        if ($cleaners->isEmpty()) {
            $this->warn('  ⚠️  No active cleaners found. Skipping.');

            return;
        }

        $this->line("  👷 Active cleaners: {$cleaners->count()}");

        // Get bookings in date range
        $bookings = Booking::whereHas('room', function ($query) use ($hotel) {
            $query->where('hotel_id', $hotel->id);
        })
            ->whereBetween('check_in_datetime', [$startDate, $endDate])
            ->with(['room', 'cleaningTask'])
            ->orderBy('check_in_datetime')
            ->get();

        $this->line("  📅 Bookings found: {$bookings->count()}");

        if ($bookings->isEmpty()) {
            $this->line("  ✓ No bookings to plan.\n");

            return;
        }

        // Process each booking
        foreach ($bookings as $booking) {
            $this->processBooking($booking, $hotel, $cleaners, $force);
        }

        $this->newLine();
    }

    protected function processBooking(Booking $booking, Hotel $hotel, $cleaners, bool $force)
    {
        $room = $booking->room;
        $date = $booking->check_in_datetime->toDateString();

        // Check if task already exists
        if ($booking->cleaningTask && ! $force) {
            $this->line("  → Booking #{$booking->id} (Room {$room->room_number}) already has task. Skipping.");

            return;
        }

        // Check for blocking issues
        $blockingIssues = Issue::where('room_id', $room->id)
            ->where('status', 'open')
            ->where('impact', 'kan_niet_gebruikt')
            ->exists();

        if ($blockingIssues) {
            $this->warn("  ⚠️  Booking #{$booking->id} (Room {$room->room_number}) - BLOCKED by issue");
            $this->stats['blocked']++;

            // Delete existing task if being replanned
            if ($booking->cleaningTask && $force) {
                $booking->cleaningTask->delete();
                $this->line('     Removed blocked task.');
            }

            return;
        }

        // Check day capacity
        $capacity = DayCapacity::where('hotel_id', $hotel->id)
            ->where('date', $date)
            ->first();

        if (! $capacity || $capacity->capacity <= 0) {
            $this->warn("  ⚠️  Booking #{$booking->id} - No capacity set for {$date}");
            $this->createUrgentIssue($booking, 'Geen capaciteit ingesteld voor '.$date);
            $this->stats['errors']++;

            return;
        }

        // Calculate planned duration (standard + 10 min buffer)
        $plannedDuration = $room->standard_duration + 10;
        $suggestedStartTime = $booking->check_in_datetime->copy()->subMinutes($plannedDuration);

        // Check if there's enough time
        if ($suggestedStartTime->lt(now())) {
            $this->warn("  ⚠️  Booking #{$booking->id} - Not enough time to clean");
            $this->createUrgentIssue($booking, 'Onvoldoende tijd om te schoonmaken voor check-in');
            $this->stats['errors']++;

            return;
        }

        // Assign cleaner with least tasks on this date (load balancing)
        $assignedCleaner = $cleaners->sortBy(function ($cleaner) use ($date) {
            return $cleaner->cleaningTasks()->where('date', $date)->count();
        })->first();

        if (! $assignedCleaner) {
            $this->error("  ❌ No cleaner available for booking #{$booking->id}");
            $this->stats['errors']++;

            return;
        }

        try {
            DB::transaction(function () use ($booking, $room, $date, $assignedCleaner, $plannedDuration, $suggestedStartTime, $force) {
                if ($booking->cleaningTask && $force) {
                    // Update existing task
                    $booking->cleaningTask->update([
                        'cleaner_id' => $assignedCleaner->id,
                        'date' => $date,
                        'deadline' => $booking->check_in_datetime,
                        'planned_duration' => $plannedDuration,
                        'suggested_start_time' => $suggestedStartTime,
                    ]);

                    $this->line("  🔄 Replanned task for booking #{$booking->id} (Room {$room->room_number}) → {$assignedCleaner->user->name}");
                    $this->stats['replanned']++;
                } else {
                    // Create new task
                    CleaningTask::create([
                        'room_id' => $room->id,
                        'cleaner_id' => $assignedCleaner->id,
                        'booking_id' => $booking->id,
                        'date' => $date,
                        'deadline' => $booking->check_in_datetime,
                        'planned_duration' => $plannedDuration,
                        'suggested_start_time' => $suggestedStartTime,
                        'status' => 'pending',
                    ]);

                    $this->line("  ✅ Planned task for booking #{$booking->id} (Room {$room->room_number}) → {$assignedCleaner->user->name}");
                    $this->stats['planned']++;
                }
            });
        } catch (\Exception $e) {
            $this->error("  ❌ Error planning booking #{$booking->id}: {$e->getMessage()}");
            $this->stats['errors']++;
        }
    }

    protected function createUrgentIssue(Booking $booking, string $description)
    {
        $room = $booking->room;

        // Create urgent issue (planning problems should not block room, so use 'graag_snel')
        $issue = Issue::create([
            'room_id' => $room->id,
            'reported_by' => \App\Models\User::getSystemUserId(),
            'impact' => 'graag_snel', // Planning issues don't block the room physically
            'note' => "[PLANNER] {$description}\n\nBoeking #{$booking->id}\nCheck-in: {$booking->check_in_datetime->format('d-m-Y H:i')}",
            'status' => 'open',
        ]);

        // Send email to owner
        if ($room->hotel && $room->hotel->owner) {
            try {
                Mail::to($room->hotel->owner->email)->send(new UrgentIssueMail($issue, $booking));
            } catch (\Exception $e) {
                $this->warn("     Failed to send email: {$e->getMessage()}");
            }
        }
    }
}
