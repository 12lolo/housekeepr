<?php

namespace App\Console\Commands;

use App\Models\Hotel;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DebugAvailability extends Command
{
    protected $signature = 'housekeepr:debug-availability {date?} {hotel_id? : The ID of the hotel (optional, shows all if not provided)}';

    protected $description = 'Debug cleaner availability for a specific date';

    public function handle()
    {
        $date = $this->argument('date') ?? now()->toDateString();
        $hotelId = $this->argument('hotel_id');
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        $dayName = Carbon::parse($date)->format('l');

        if ($hotelId) {
            $hotel = Hotel::find($hotelId);
            if (! $hotel) {
                $this->error("Hotel with ID {$hotelId} not found!");

                return 1;
            }
            $this->info("=== Cleaner Availability Debug for {$hotel->name} ===");
        } else {
            $this->info('=== Cleaner Availability Debug (ALL Hotels) ===');
        }

        $this->info("Date: $date");
        $this->info("Day of week: $dayOfWeek ($dayName)");
        $this->newLine();

        $hotelsQuery = Hotel::with(['cleaners.user']);
        if ($hotelId) {
            $hotelsQuery->where('id', $hotelId);
        }
        $hotels = $hotelsQuery->get();

        foreach ($hotels as $hotel) {
            $this->info("🏨 {$hotel->name} (ID: {$hotel->id})");

            $cleaners = $hotel->cleaners()->where('status', 'active')->with('user')->get();

            if ($cleaners->isEmpty()) {
                $this->warn('  No active cleaners found');

                continue;
            }

            foreach ($cleaners as $cleaner) {
                $isAvailable = $cleaner->isAvailableOnDay($dayOfWeek);
                $workingDays = $cleaner->getWorkingDaysText();
                $status = $isAvailable ? '✓ AVAILABLE' : '✗ Not available';

                $this->line("  {$cleaner->user->name} (ID: {$cleaner->id})");
                $this->line("    Days: {$workingDays} - $status");
            }

            // Test the where query
            $dayColumn = match ($dayOfWeek) {
                0 => 'works_sunday',
                1 => 'works_monday',
                2 => 'works_tuesday',
                3 => 'works_wednesday',
                4 => 'works_thursday',
                5 => 'works_friday',
                6 => 'works_saturday',
            };

            $available = $hotel->cleaners()
                ->where('status', 'active')
                ->where($dayColumn, true)
                ->count();

            $this->line("  → Query result: $available available cleaner(s)");
            $this->newLine();
        }

        return 0;
    }
}
