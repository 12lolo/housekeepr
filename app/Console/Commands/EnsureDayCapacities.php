<?php

namespace App\Console\Commands;

use App\Models\DayCapacity;
use App\Models\Hotel;
use Illuminate\Console\Command;

class EnsureDayCapacities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'housekeepr:ensure-capacities {hotel_id? : The ID of the hotel (optional, processes all if not provided)} {--days=60 : Number of days into the future} {--past-days=7 : Number of days into the past}';

    protected $description = 'Ensure day capacities exist for all hotels for the next N days';

    public function handle()
    {
        $days = (int) $this->option('days');
        $pastDays = (int) $this->option('past-days');
        $hotelId = $this->argument('hotel_id');

        if ($hotelId) {
            $hotel = Hotel::find($hotelId);
            if (! $hotel) {
                $this->error("Hotel with ID {$hotelId} not found!");

                return 1;
            }
            $this->info("Ensuring day capacities for {$hotel->name} ({$pastDays} days past to {$days} days future)...");
            $hotels = collect([$hotel]);
        } else {
            $this->info("Ensuring day capacities for ALL hotels ({$pastDays} days past to {$days} days future)...");
            $hotels = Hotel::all();
        }

        if ($hotels->isEmpty()) {
            $this->warn('No hotels found');

            return 1;
        }

        $created = 0;
        $skipped = 0;

        foreach ($hotels as $hotel) {
            $this->info("Processing {$hotel->name}...");

            // Get default capacity (number of active cleaners)
            $defaultCapacity = $hotel->cleaners()->where('status', 'active')->count();

            if ($defaultCapacity === 0) {
                $this->warn("  ⚠️  No active cleaners for {$hotel->name}, setting capacity to 1");
                $defaultCapacity = 1;
            }

            // Create capacities for past days
            for ($i = -$pastDays; $i < 0; $i++) {
                $date = now()->addDays($i)->toDateString();

                try {
                    DayCapacity::firstOrCreate(
                        ['hotel_id' => $hotel->id, 'date' => $date],
                        ['capacity' => $defaultCapacity]
                    );
                    $created++;
                } catch (\Exception $e) {
                    $skipped++;
                }
            }

            // Create capacities for future days
            for ($i = 0; $i < $days; $i++) {
                $date = now()->addDays($i)->toDateString();

                try {
                    DayCapacity::firstOrCreate(
                        ['hotel_id' => $hotel->id, 'date' => $date],
                        ['capacity' => $defaultCapacity]
                    );
                    $created++;
                } catch (\Exception $e) {
                    $skipped++;
                }
            }

            $this->info("  ✅ {$hotel->name}: {$defaultCapacity} cleaners/day");
        }

        $this->newLine();
        $this->info('✅ Complete!');
        $this->info("   Created: {$created} new capacity entries");
        $this->info("   Skipped: {$skipped} existing entries");

        return 0;
    }
}
