<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            if (!Schema::hasColumn('hotels', 'address')) {
                $table->text('address')->nullable()->after('owner_id');
            }
            if (!Schema::hasColumn('hotels', 'street')) {
                $table->string('street')->nullable()->after('address');
            }
            if (!Schema::hasColumn('hotels', 'house_number')) {
                $table->string('house_number', 20)->nullable()->after('street');
            }
            if (!Schema::hasColumn('hotels', 'house_number_addition')) {
                $table->string('house_number_addition', 10)->nullable()->after('house_number');
            }
            if (!Schema::hasColumn('hotels', 'postal_code')) {
                $table->string('postal_code', 20)->nullable()->after('house_number_addition');
            }
            if (!Schema::hasColumn('hotels', 'city')) {
                $table->string('city', 100)->nullable()->after('postal_code');
            }
            if (!Schema::hasColumn('hotels', 'country')) {
                $table->string('country', 100)->nullable()->after('city');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $columns = ['address', 'street', 'house_number', 'house_number_addition', 'postal_code', 'city', 'country'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('hotels', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
