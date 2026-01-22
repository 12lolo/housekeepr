<?php

namespace App\Console\Commands;

use App\Models\Issue;
use Illuminate\Console\Command;

class CleanupDuplicateIssues extends Command
{
    protected $signature = 'housekeepr:cleanup-duplicate-issues {--dry-run : Show what would be deleted without actually deleting}';

    protected $description = 'Remove duplicate auto-generated issues, keeping only the oldest one per booking';

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->info('🔍 DRY RUN MODE - No issues will be deleted');
            $this->newLine();
        }

        $this->info('Searching for duplicate issues...');

        // Get all auto-generated issues grouped by booking ID
        $allIssues = Issue::where('note', 'like', 'URGENT:%')
            ->where('status', 'open')
            ->orderBy('created_at', 'asc')
            ->get();

        // Group by booking ID extracted from note
        $groupedIssues = $allIssues->groupBy(function ($issue) {
            // Extract booking ID from note like "Boeking #123"
            if (preg_match('/Boeking #(\d+)/', $issue->note, $matches)) {
                $bookingId = $matches[1];

                // Also identify issue type to group separately
                if (str_contains($issue->note, 'Onvoldoende tijd')) {
                    return "booking_{$bookingId}_insufficient_time";
                } elseif (str_contains($issue->note, 'Geen schoonmakers')) {
                    return "booking_{$bookingId}_no_cleaners";
                } elseif (str_contains($issue->note, 'Kan schoonmaak niet inplannen')) {
                    return "booking_{$bookingId}_conflict";
                }
            }

            return 'ungrouped_'.$issue->id;
        });

        $totalDeleted = 0;
        $groupsProcessed = 0;

        foreach ($groupedIssues as $key => $issues) {
            if ($issues->count() <= 1) {
                continue; // No duplicates
            }

            $groupsProcessed++;
            $keep = $issues->first(); // Keep oldest
            $duplicates = $issues->skip(1); // Delete rest

            $this->warn("Found {$duplicates->count()} duplicate(s) for: {$key}");
            $this->line("  Keeping: Issue #{$keep->id} (created {$keep->created_at->diffForHumans()})");

            foreach ($duplicates as $duplicate) {
                $this->line("  Deleting: Issue #{$duplicate->id} (created {$duplicate->created_at->diffForHumans()})");

                if (! $isDryRun) {
                    $duplicate->delete();
                }

                $totalDeleted++;
            }

            $this->newLine();
        }

        if ($groupsProcessed === 0) {
            $this->info('✅ No duplicate issues found!');

            return 0;
        }

        if ($isDryRun) {
            $this->info("Would delete {$totalDeleted} duplicate issues across {$groupsProcessed} groups");
            $this->info('Run without --dry-run to actually delete them');
        } else {
            $this->info("✅ Deleted {$totalDeleted} duplicate issues across {$groupsProcessed} groups");
        }

        return 0;
    }
}
