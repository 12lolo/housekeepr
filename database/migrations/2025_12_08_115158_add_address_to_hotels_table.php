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
            $table->text('address')->nullable()->after('owner_id');
            $table->string('street')->nullable()->after('address');
            $table->string('house_number', 20)->nullable()->after('street');
            $table->string('house_number_addition', 10)->nullable()->after('house_number');
            $table->string('postal_code', 20)->nullable()->after('house_number_addition');
            $table->string('city', 100)->nullable()->after('postal_code');
            $table->string('country', 100)->nullable()->after('city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropColumn([
                'address',
                'street',
                'house_number',
                'house_number_addition',
                'postal_code',
                'city',
                'country',
            ]);
        });
    }
};
