<?php

namespace Tests\Feature\Cleaner;

use App\Models\User;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\Cleaner;
use App\Models\CleaningTask;
use App\Models\TaskLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskActionsTest extends TestCase
{
    use RefreshDatabase;

    protected $cleanerUser;
    protected $cleaner;
    protected $hotel;
    protected $room;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hotel = Hotel::factory()->create();
        $this->room = Room::factory()->create(['hotel_id' => $this->hotel->id]);

        $this->cleanerUser = User::factory()->create([
            'role' => 'cleaner',
            'status' => 'active',
        ]);

        $this->cleaner = Cleaner::factory()->create([
            'user_id' => $this->cleanerUser->id,
            'hotel_id' => $this->hotel->id,
        ]);
    }

    /** @test */
    public function cleaner_can_start_pending_task()
    {
        $task = CleaningTask::factory()->create([
            'cleaner_id' => $this->cleaner->id,
            'room_id' => $this->room->id,
            'status' => 'pending',
            'date' => today(),
        ]);

        $response = $this->actingAs($this->cleanerUser)
            ->post(route('cleaner.tasks.start', $task));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('cleaning_tasks', [
            'id' => $task->id,
            'status' => 'in_progress',
        ]);

        $this->assertDatabaseHas('task_logs', [
            'cleaning_task_id' => $task->id,
            'action' => 'started',
        ]);
    }

    /** @test */
    public function cleaner_cannot_start_already_started_task()
    {
        $task = CleaningTask::factory()->create([
            'cleaner_id' => $this->cleaner->id,
            'room_id' => $this->room->id,
            'status' => 'in_progress',
        ]);

        $response = $this->actingAs($this->cleanerUser)
            ->post(route('cleaner.tasks.start', $task));

        $response->assertSessionHas('error');
    }

    /** @test */
    public function cleaner_can_stop_in_progress_task()
    {
        $task = CleaningTask::factory()->create([
            'cleaner_id' => $this->cleaner->id,
            'room_id' => $this->room->id,
            'status' => 'in_progress',
            'actual_start_time' => now()->subMinutes(30),
        ]);

        $response = $this->actingAs($this->cleanerUser)
            ->post(route('cleaner.tasks.stop', $task));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('task_logs', [
            'cleaning_task_id' => $task->id,
            'action' => 'stopped',
        ]);
    }

    /** @test */
    public function cleaner_can_complete_task()
    {
        $task = CleaningTask::factory()->create([
            'cleaner_id' => $this->cleaner->id,
            'room_id' => $this->room->id,
            'status' => 'in_progress',
            'actual_start_time' => now()->subMinutes(45),
        ]);

        $response = $this->actingAs($this->cleanerUser)
            ->post(route('cleaner.tasks.complete', $task));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $task->refresh();

        $this->assertEquals('completed', $task->status);
        $this->assertNotNull($task->actual_end_time);
        $this->assertNotNull($task->actual_duration);

        $this->assertDatabaseHas('task_logs', [
            'cleaning_task_id' => $task->id,
            'action' => 'completed',
        ]);
    }

    /** @test */
    public function cleaner_cannot_complete_task_without_starting_it()
    {
        $task = CleaningTask::factory()->create([
            'cleaner_id' => $this->cleaner->id,
            'room_id' => $this->room->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->cleanerUser)
            ->post(route('cleaner.tasks.complete', $task));

        $response->assertSessionHas('error');

        $this->assertDatabaseHas('cleaning_tasks', [
            'id' => $task->id,
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function cleaner_cannot_access_other_cleaner_tasks()
    {
        $otherCleaner = Cleaner::factory()->create();
        $task = CleaningTask::factory()->create([
            'cleaner_id' => $otherCleaner->id,
            'room_id' => $this->room->id,
        ]);

        $response = $this->actingAs($this->cleanerUser)
            ->post(route('cleaner.tasks.start', $task));

        $response->assertStatus(403);
    }

    /** @test */
    public function task_duration_calculation_accounts_for_pauses()
    {
        $startTime = now()->subHours(2);

        $task = CleaningTask::factory()->create([
            'cleaner_id' => $this->cleaner->id,
            'room_id' => $this->room->id,
            'status' => 'in_progress',
            'actual_start_time' => $startTime,
        ]);

        // Simulate pause: stop after 30 mins, resume after 15 min break
        TaskLog::create([
            'cleaning_task_id' => $task->id,
            'action' => 'started',
            'timestamp' => $startTime,
        ]);

        TaskLog::create([
            'cleaning_task_id' => $task->id,
            'action' => 'stopped',
            'timestamp' => $startTime->copy()->addMinutes(30),
        ]);

        TaskLog::create([
            'cleaning_task_id' => $task->id,
            'action' => 'started',
            'timestamp' => $startTime->copy()->addMinutes(45),
        ]);

        // Complete the task now (75 mins after pause ended = 30 + 75 = 105 mins work)
        $this->actingAs($this->cleanerUser)
            ->post(route('cleaner.tasks.complete', $task));

        $task->refresh();

        // Should be approximately 105 minutes (30 before pause + 75 after)
        // Allow small margin for test execution time
        $this->assertGreaterThanOrEqual(100, $task->actual_duration);
        $this->assertLessThanOrEqual(110, $task->actual_duration);
    }
}
