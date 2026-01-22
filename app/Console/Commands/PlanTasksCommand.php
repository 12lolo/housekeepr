<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Hotel;
use App\Models\Issue;
use App\Services\CleaningScheduler;
use Carbon\Carbon;
use Illuminate\Console\Command;

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

        // Check if task already exists
        if ($booking->cleaningTask && ! $force) {
            $this->line("  → Booking #{$booking->id} (Room {$room->room_number}) already has task. Skipping.");

            return;
        }

        // Delete existing task if being replanned
        if ($booking->cleaningTask && $force) {
            $booking->cleaningTask->delete();
            $this->line("  🔄 Replanning booking #{$booking->id} (Room {$room->room_number})");
            $this->stats['replanned']++;
        }

        // Use CleaningScheduler to schedule the booking
        $scheduler = app(CleaningScheduler::class);

        try {
            $task = $scheduler->scheduleBooking($booking);

            if ($task) {
                $this->line("  ✅ Planned task for booking #{$booking->id} (Room {$room->room_number}) → {$task->cleaner->user->name}");

                if (! $force) {
                    $this->stats['planned']++;
                }
            } else {
                // Scheduler couldn't create task - check why
                $blockingIssues = Issue::where('room_id', $room->id)
                    ->where('status', 'open')
                    ->where('impact', 'kan_niet_gebruikt')
                    ->exists();

                if ($blockingIssues) {
                    $this->warn("  ⚠️  Booking #{$booking->id} (Room {$room->room_number}) - BLOCKED by issue");
                    $this->stats['blocked']++;
                } else {
                    $this->warn("  ⚠️  Booking #{$booking->id} - Could not schedule (check issues)");
                    $this->stats['errors']++;
                }
            }
        } catch (\Exception $e) {
            $this->error("  ❌ Error planning booking #{$booking->id}: {$e->getMessage()}");
            $this->stats['errors']++;
        }
    }
}
