<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Cleaner;
use App\Models\CleaningTask;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CleaningSchedulerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $owner = User::factory()->create([
            'role' => 'owner',
            'status' => 'active',
        ]);

        $this->hotel = Hotel::create([
            'owner_id' => $owner->id,
            'name' => 'Test Hotel',
        ]);

        $this->room = Room::create([
            'hotel_id' => $this->hotel->id,
            'room_number' => '101',
            'room_type' => 'Standard',
            'standard_duration' => 60,
            'checkout_time' => '11:00',
        ]);

        $cleanerUser = User::factory()->create([
            'role' => 'cleaner',
            'status' => 'active',
        ]);

        $this->cleaner = Cleaner::create([
            'user_id' => $cleanerUser->id,
            'hotel_id' => $this->hotel->id,
            'status' => 'active',
            // Set cleaner to work all days
            'works_monday' => true,
            'works_tuesday' => true,
            'works_wednesday' => true,
            'works_thursday' => true,
            'works_friday' => true,
            'works_saturday' => true,
            'works_sunday' => true,
        ]);
    }

    /** @test */
    public function it_automatically_creates_cleaning_task_when_booking_is_created()
    {
        // Arrange
        $checkInDate = now()->addDay()->setTime(15, 0);
        $checkOutDate = now()->addDays(3)->setTime(11, 0);

        $tasksBefore = CleaningTask::count();

        // Act
        $booking = Booking::create([
            'room_id' => $this->room->id,
            'guest_name' => 'John Doe',
            'check_in' => $checkInDate->toDateString(),
            'check_out' => $checkOutDate->toDateString(),
            'check_in_datetime' => $checkInDate,
            'check_out_datetime' => $checkOutDate,
        ]);

        // Assert
        $tasksAfter = CleaningTask::count();
        $this->assertEquals($tasksBefore + 1, $tasksAfter, 'A cleaning task should be created');

        $task = $booking->cleaningTask;
        $this->assertNotNull($task, 'Booking should have a cleaning task');

        // Check task details
        $this->assertEquals($this->room->id, $task->room_id);
        $this->assertEquals($this->cleaner->id, $task->cleaner_id);
        $this->assertEquals($checkInDate->toDateString(), $task->date->toDateString());
        $this->assertEquals($checkInDate->format('Y-m-d H:i'), $task->deadline->format('Y-m-d H:i'));

        // Check timing calculation
        $expectedDuration = $this->room->standard_duration + 10; // 70 minutes
        $this->assertEquals($expectedDuration, $task->planned_duration);

        // New algorithm: suggested start is at checkout time (11:00)
        $checkoutDateTime = $checkInDate->copy()->setTimeFromTimeString($this->room->checkout_time);
        $this->assertEquals($checkoutDateTime->format('H:i'), $task->suggested_start_time->format('H:i'));

        $this->assertEquals('pending', $task->status);
    }

    /** @test */
    public function it_assigns_cleaner_with_least_workload()
    {
        // Create a second cleaner
        $cleanerUser2 = User::factory()->create([
            'role' => 'cleaner',
            'status' => 'active',
        ]);

        $cleaner2 = Cleaner::create([
            'user_id' => $cleanerUser2->id,
            'hotel_id' => $this->hotel->id,
            'status' => 'active',
            // Set cleaner to work all days
            'works_monday' => true,
            'works_tuesday' => true,
            'works_wednesday' => true,
            'works_thursday' => true,
            'works_friday' => true,
            'works_saturday' => true,
            'works_sunday' => true,
        ]);

        $checkInDate = now()->addDay()->setTime(15, 0);

        // Give first cleaner an existing task
        CleaningTask::create([
            'room_id' => $this->room->id,
            'cleaner_id' => $this->cleaner->id,
            'date' => $checkInDate->toDateString(),
            'deadline' => $checkInDate,
            'planned_duration' => 60,
            'status' => 'pending',
        ]);

        // Create a booking
        $booking = Booking::create([
            'room_id' => $this->room->id,
            'guest_name' => 'Jane Doe',
            'check_in' => $checkInDate->toDateString(),
            'check_out' => $checkInDate->addDays(2)->toDateString(),
            'check_in_datetime' => $checkInDate,
            'check_out_datetime' => $checkInDate->copy()->addDays(2),
        ]);

        // The new task should be assigned to cleaner2 (fewer tasks)
        $task = $booking->cleaningTask;
        $this->assertNotNull($task);
        $this->assertEquals($cleaner2->id, $task->cleaner_id, 'Task should be assigned to cleaner with fewer tasks');
    }

    /** @test */
    public function it_does_not_create_task_if_already_exists()
    {
        $checkInDate = now()->addDay()->setTime(15, 0);

        $booking = Booking::create([
            'room_id' => $this->room->id,
            'guest_name' => 'John Doe',
            'check_in' => $checkInDate->toDateString(),
            'check_out' => $checkInDate->addDays(2)->toDateString(),
            'check_in_datetime' => $checkInDate,
            'check_out_datetime' => $checkInDate->copy()->addDays(2),
        ]);

        $tasksBefore = CleaningTask::count();

        // Try to create another task for same booking (shouldn't happen in normal flow)
        event(new \App\Events\BookingCreated($booking));

        $tasksAfter = CleaningTask::count();
        $this->assertEquals($tasksBefore, $tasksAfter, 'Should not create duplicate task');
    }
}
