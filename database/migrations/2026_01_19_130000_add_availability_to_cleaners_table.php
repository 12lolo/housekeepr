<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cleaners', function (Blueprint $table) {
            // Add boolean columns for each day of the week
            $table->boolean('works_monday')->default(true)->after('status');
            $table->boolean('works_tuesday')->default(true)->after('works_monday');
            $table->boolean('works_wednesday')->default(true)->after('works_tuesday');
            $table->boolean('works_thursday')->default(true)->after('works_wednesday');
            $table->boolean('works_friday')->default(true)->after('works_thursday');
            $table->boolean('works_saturday')->default(false)->after('works_friday');
            $table->boolean('works_sunday')->default(false)->after('works_saturday');
        });

        // Migrate existing data from cleaner_availability to cleaners table
        $cleaners = DB::table('cleaners')->get();
        foreach ($cleaners as $cleaner) {
            $availability = DB::table('cleaner_availability')
                ->where('cleaner_id', $cleaner->id)
                ->pluck('day_of_week')
                ->toArray();

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
