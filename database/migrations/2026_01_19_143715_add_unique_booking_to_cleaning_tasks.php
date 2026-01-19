<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, clean up any existing duplicates
        $this->cleanupDuplicates();

        // Add unique index on booking_id
        // SQLite doesn't enforce unique on NULL values, so this is safe
        Schema::table('cleaning_tasks', function (Blueprint $table) {
            $table->unique('booking_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cleaning_tasks', function (Blueprint $table) {
            $table->dropUnique(['booking_id']);
        });
    }

    /**
     * Clean up duplicate cleaning tasks before adding unique constraint
     */
    private function cleanupDuplicates(): void
    {
        // Find all booking_ids with multiple tasks
        $duplicates = DB::table('cleaning_tasks')
            ->select('booking_id', DB::raw('COUNT(*) as count'))
            ->whereNotNull('booking_id')
            ->groupBy('booking_id')
            ->having('count', '>', 1)
            ->get();

        foreach ($duplicates as $duplicate) {
            // Get all tasks for this booking, ordered by ID (keep oldest)
            $tasks = DB::table('cleaning_tasks')
                ->where('booking_id', $duplicate->booking_id)
                ->orderBy('id')
                ->get();

            // Delete all but the first
            $taskIdsToDelete = $tasks->skip(1)->pluck('id');

            if ($taskIdsToDelete->isNotEmpty()) {
                DB::table('cleaning_tasks')
                    ->whereIn('id', $taskIdsToDelete)
                    ->delete();
            }
        }
    }
};
