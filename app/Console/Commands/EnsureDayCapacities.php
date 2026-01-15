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
    protected $signature = 'housekeepr:ensure-capacities {--days=60 : Number of days into the future}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ensure day capacities exist for all hotels for the next N days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');

        $this->info("Ensuring day capacities for the next {$days} days...");

        $hotels = Hotel::all();

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

            for ($i = 0; $i < $days; $i++) {
                $date = now()->addDays($i)->toDateString();

                // Check if capacity already exists
                $exists = DayCapacity::where('hotel_id', $hotel->id)
                    ->where('date', $date)
                    ->exists();

                if (!$exists) {
                    DayCapacity::create([
                        'hotel_id' => $hotel->id,
                        'date' => $date,
                        'capacity' => $defaultCapacity,
                    ]);
                    $created++;
                } else {
                    $skipped++;
                }
            }

            $this->info("  ✅ {$hotel->name}: {$defaultCapacity} cleaners/day");
        }

        $this->newLine();
        $this->info("✅ Complete!");
        $this->info("   Created: {$created} new capacity entries");
        $this->info("   Skipped: {$skipped} existing entries");

        return 0;
    }
}
