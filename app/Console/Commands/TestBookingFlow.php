<?php

namespace App\Console\Commands;

use App\Events\BookingCreated;
use App\Models\Booking;
use App\Models\Hotel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Event;

class TestBookingFlow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:booking-flow';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test booking creation and cleaning task generation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Testing Booking → Cleaning Task Flow');
        $this->newLine();

        // 1. Check prerequisites
        $this->info('Step 1: Checking prerequisites...');

        $hotel = Hotel::first();
        if (! $hotel) {
            $this->error('❌ No hotel found');

            return 1;
        }
        $this->info("✅ Hotel found: {$hotel->name} (ID: {$hotel->id})");

        $room = $hotel->rooms()->first();
        if (! $room) {
            $this->error('❌ No room found');

            return 1;
        }
        $this->info("✅ Room found: {$room->room_number} (ID: {$room->id})");

        $cleaners = $hotel->cleaners()->where('status', 'active')->count();
        $this->info("✅ Active cleaners: {$cleaners}");

        // 2. Check event listeners
        $this->newLine();
        $this->info('Step 2: Checking event listeners...');
        $listeners = Event::getListeners(BookingCreated::class);
        $this->info('Registered listeners for BookingCreated: '.count($listeners));
        foreach ($listeners as $listener) {
            $this->info('  - '.(is_string($listener) ? $listener : get_class($listener)));
        }

        // 3. Create test booking
        $this->newLine();
        $this->info('Step 3: Creating test booking...');

        $checkInDate = now()->addDays(2);
        $checkOutDate = now()->addDays(3);

        $booking = Booking::create([
            'room_id' => $room->id,
            'guest_name' => 'Test Guest (CLI)',
            'check_in' => $checkInDate->toDateString(),
            'check_out' => $checkOutDate->toDateString(),
            'check_in_datetime' => $checkInDate->setTime(14, 0),
            'check_out_datetime' => $checkOutDate->setTime(11, 0),
            'notes' => 'Test booking via CLI command',
        ]);

        $this->info("✅ Booking created (ID: {$booking->id})");

        // 4. Check if cleaning task was created
        $this->newLine();
        $this->info('Step 4: Checking for cleaning task...');

        sleep(1); // Give time for event processing
        $booking->refresh();
        $booking->load('cleaningTask');

        if ($booking->cleaningTask) {
            $this->info('✅ Cleaning task created!');
            $this->info("   Task ID: {$booking->cleaningTask->id}");
            $this->info("   Cleaner ID: {$booking->cleaningTask->cleaner_id}");
            $this->info("   Date: {$booking->cleaningTask->date}");
            $this->info("   Status: {$booking->cleaningTask->status}");
        } else {
            $this->error('❌ No cleaning task created');

            // Check for issues
            $issues = \App\Models\Issue::where('room_id', $room->id)
                ->where('status', 'open')
                ->get();

            if ($issues->isNotEmpty()) {
                $this->warn("⚠️  Found {$issues->count()} open issues for this room:");
                foreach ($issues as $issue) {
                    $this->warn("   - {$issue->note} ({$issue->impact})");
                }
            } else {
                $this->warn("   No open issues found - check cleaner availability for {$checkInDate->format('l')}");
            }
        }

        // 5. Manual event test
        $this->newLine();
        $this->info('Step 5: Manually firing BookingCreated event...');

        try {
            event(new BookingCreated($booking));
            $this->info('✅ Event fired manually');

            sleep(1);
            $booking->refresh();
            $booking->load('cleaningTask');

            if ($booking->cleaningTask) {
                $this->info('✅ Task now exists after manual event fire!');
            } else {
                $this->warn('⚠️  Still no task after manual event fire');
            }
        } catch (\Exception $e) {
            $this->error('❌ Error firing event: '.$e->getMessage());
        }

        $this->newLine();
        $this->info('Test complete!');

        return 0;
    }
}
