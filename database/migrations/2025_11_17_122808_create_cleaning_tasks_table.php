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
        Schema::create('cleaning_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->foreignId('cleaner_id')->constrained()->onDelete('cascade');
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->date('date'); // Datum waarop taak moet gebeuren
            $table->dateTime('deadline'); // = booking.check_in_datetime (moet klaar zijn voor deze tijd)
            $table->integer('planned_duration'); // minuten (standard_duration + buffer)
            $table->dateTime('suggested_start_time')->nullable(); // Aanbevolen starttijd (berekend)
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->dateTime('actual_start_time')->nullable();
            $table->dateTime('actual_end_time')->nullable();
            $table->integer('actual_duration')->nullable(); // minuten
            $table->timestamps();

            $table->index(['cleaner_id', 'date']);
            $table->index(['date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cleaning_tasks');
    }
};
