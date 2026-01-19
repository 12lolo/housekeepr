<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Only add columns if they don't exist
        if (! Schema::hasColumn('cleaners', 'works_monday')) {
            Schema::table('cleaners', function (Blueprint $table) {
                $table->boolean('works_monday')->default(true);
                $table->boolean('works_tuesday')->default(true);
                $table->boolean('works_wednesday')->default(true);
                $table->boolean('works_thursday')->default(true);
                $table->boolean('works_friday')->default(true);
                $table->boolean('works_saturday')->default(false);
                $table->boolean('works_sunday')->default(false);
            });
        }

        // Migrate data from cleaner_availability if it exists
        if (Schema::hasTable('cleaner_availability')) {
            $cleaners = DB::table('cleaners')->get();
            foreach ($cleaners as $cleaner) {
                $availability = DB::table('cleaner_availability')
                    ->where('cleaner_id', $cleaner->id)
                    ->pluck('day_of_week')
                    ->toArray();

                if (! empty($availability)) {
                    DB::table('cleaners')->where('id', $cleaner->id)->update([
                        'works_sunday' => in_array(0, $availability),
                        'works_monday' => in_array(1, $availability),
                        'works_tuesday' => in_array(2, $availability),
                        'works_wednesday' => in_array(3, $availability),
                        'works_thursday' => in_array(4, $availability),
                        'works_friday' => in_array(5, $availability),
                        'works_saturday' => in_array(6, $availability),
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::table('cleaners', function (Blueprint $table) {
            $table->dropColumn([
                'works_monday',
                'works_tuesday',
                'works_wednesday',
                'works_thursday',
                'works_friday',
                'works_saturday',
                'works_sunday',
            ]);
        });
    }
};
