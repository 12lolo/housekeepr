<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Hotel;
use Illuminate\Support\Facades\DB;

class DeleteAllOwners extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'owners:delete-all {--force : Skip confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all owner users and their associated hotels, rooms, bookings, cleaners, etc.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get all owner users
        $owners = User::where('role', 'owner')->get();

        if ($owners->isEmpty()) {
            $this->info('No owner users found.');
            return 0;
        }

        $this->warn("Found {$owners->count()} owner user(s) to delete:");

        foreach ($owners as $owner) {
            $hotel = $owner->hotel;
            $hotelInfo = $hotel ? " (Hotel: {$hotel->name})" : " (No hotel)";
            $this->line("  - {$owner->email}{$hotelInfo}");
        }

        // Confirmation
        if (!$this->option('force')) {
            if (!$this->confirm('Are you sure you want to delete all these owners and their data? This cannot be undone!')) {
                $this->info('Deletion cancelled.');
                return 0;
            }
        }

        $this->info('Starting deletion process...');

        DB::beginTransaction();

        try {
            $deletedCount = 0;

            foreach ($owners as $owner) {
                $this->line("Deleting owner: {$owner->email}");

                // Get hotel before deleting owner
                $hotel = $owner->hotel;

                if ($hotel) {
                    $this->line("  └─ Deleting hotel: {$hotel->name}");

                    // Delete all related data (cascade will handle most of this)
                    // But we'll explicitly delete some for logging
                    $roomsCount = $hotel->rooms()->count();
                    $cleanersCount = $hotel->cleaners()->count();

                    if ($roomsCount > 0) {
                        $this->line("     └─ {$roomsCount} room(s)");
                    }
                    if ($cleanersCount > 0) {
                        $this->line("     └─ {$cleanersCount} cleaner(s)");
                    }

                    // Delete hotel (cascade should handle rooms, bookings, cleaning tasks, etc.)
                    $hotel->delete();
                }

                // Delete the owner user
                $owner->delete();
                $deletedCount++;
            }

            DB::commit();

            $this->info("✅ Successfully deleted {$deletedCount} owner user(s) and their associated data.");

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Error occurred during deletion: ' . $e->getMessage());
            $this->error('All changes have been rolled back.');

            return 1;
        }
    }
}
