<?php

namespace App\Console\Commands;

use App\Models\CleaningTask;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupDuplicateTasks extends Command
{
    protected $signature = 'housekeepr:cleanup-duplicate-tasks {hotel_id?}';

    protected $description = 'Remove duplicate cleaning tasks (keeps oldest for each booking)';

    public function handle()
    {
        $hotelId = $this->argument('hotel_id');

        $this->info('Finding duplicate cleaning tasks...');

        // Find tasks with duplicate booking_id
        $query = CleaningTask::select('booking_id', DB::raw('COUNT(*) as count'))
            ->whereNotNull('booking_id')
            ->groupBy('booking_id')
            ->having('count', '>', 1);

        if ($hotelId) {
            $query->whereHas('room', function ($q) use ($hotelId) {
                $q->where('hotel_id', $hotelId);
            });
        }

        $duplicates = $query->get();

        if ($duplicates->isEmpty()) {
            $this->info('✓ No duplicate tasks found');

            return 0;
        }

        $totalDeleted = 0;

        foreach ($duplicates as $duplicate) {
            $bookingId = $duplicate->booking_id;
            $count = $duplicate->count;

            // Get all tasks for this booking, ordered by ID (oldest first)
            $tasks = CleaningTask::where('booking_id', $bookingId)
                ->orderBy('id')
                ->get();

            // Keep the first one, delete the rest
            $keepTask = $tasks->first();
            $deleteTasks = $tasks->skip(1);

            $this->line("  Booking #{$bookingId}: Keeping task #{$keepTask->id}, deleting ".$deleteTasks->count().' duplicate(s)');

            foreach ($deleteTasks as $task) {
                $task->delete();
                $totalDeleted++;
            }
        }

        $this->newLine();
        $this->info("✅ Deleted {$totalDeleted} duplicate task(s)");

        activity()
            ->log("Cleaned up {$totalDeleted} duplicate cleaning tasks via command");

        return 0;
    }
}
