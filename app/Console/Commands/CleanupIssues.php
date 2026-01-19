<?php

namespace App\Console\Commands;

use App\Models\Issue;
use Illuminate\Console\Command;

class CleanupIssues extends Command
{
    protected $signature = 'housekeepr:cleanup-issues {hotel_id?} {--auto-only : Only delete auto-generated issues} {--all : Delete all issues}';

    protected $description = 'Clean up issues (auto-generated or all)';

    public function handle()
    {
        $hotelId = $this->argument('hotel_id');
        $autoOnly = $this->option('auto-only');
        $all = $this->option('all');

        if (! $autoOnly && ! $all) {
            // Default to auto-only if no option specified
            $autoOnly = true;
        }

        $query = Issue::query();

        // Filter by hotel if specified
        if ($hotelId) {
            $query->whereHas('room', function ($q) use ($hotelId) {
                $q->where('hotel_id', $hotelId);
            });

            $hotel = \App\Models\Hotel::findOrFail($hotelId);
            $this->info("Cleaning issues for {$hotel->name}...");
        } else {
            $this->info('Cleaning issues for all hotels...');
        }

        // Filter by type
        if ($autoOnly) {
            // Auto-generated issues have "URGENT:" in the note
            $query->where('note', 'like', 'URGENT:%');
            $this->info('Mode: Auto-generated issues only');
        } else {
            $this->info('Mode: All issues');
        }

        $count = $query->count();

        if ($count === 0) {
            $this->info('✓ No issues to clean up');

            return 0;
        }

        // Show what will be deleted
        $this->warn("Found {$count} issue(s) to delete:");
        $issues = $query->limit(5)->get();
        foreach ($issues as $issue) {
            $room = $issue->room;
            $hotel = $room ? $room->hotel : null;
            $this->line("  - Issue #{$issue->id}: {$issue->note} (Room {$room->room_number} @ {$hotel->name})");
        }

        if ($count > 5) {
            $this->line('  ... and '.($count - 5).' more');
        }

        // Confirm deletion
        if (! $this->confirm("Delete {$count} issue(s)?", true)) {
            $this->info('Cancelled');

            return 0;
        }

        // Delete issues
        $deleted = $query->delete();

        $this->info("✅ Deleted {$deleted} issue(s)");

        activity()
            ->log("Cleaned up {$deleted} issues via command");

        return 0;
    }
}
