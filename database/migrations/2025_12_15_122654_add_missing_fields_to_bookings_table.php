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
        Schema::table('bookings', function (Blueprint $table) {
            // Add guest_name field
            $table->string('guest_name')->after('room_id');

            // Add check_in and check_out date fields
            $table->date('check_in')->after('guest_name');
            $table->date('check_out')->after('check_in');

            // Add check_out_datetime field
            $table->dateTime('check_out_datetime')->nullable()->after('check_in_datetime');

            // Update index
            $table->dropIndex(['room_id', 'check_in_datetime']);
            $table->index(['room_id', 'check_in', 'check_out']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['guest_name', 'check_in', 'check_out', 'check_out_datetime']);
            $table->dropIndex(['room_id', 'check_in', 'check_out']);
            $table->index(['room_id', 'check_in_datetime']);
        });
    }
};
