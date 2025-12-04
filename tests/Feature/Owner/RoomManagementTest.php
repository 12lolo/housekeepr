<?php

namespace Tests\Feature\Owner;

use App\Models\User;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\CleaningTask;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $owner;
    protected $hotel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create(['role' => 'owner', 'status' => 'active']);
        $this->hotel = Hotel::factory()->create(['owner_id' => $this->owner->id]);
    }

    /** @test */
    public function owner_can_view_rooms_list()
    {
        $response = $this->actingAs($this->owner)
            ->get(route('owner.rooms.index'));

        $response->assertStatus(200);
        $response->assertViewIs('owner.rooms.index');
    }

    /** @test */
    public function owner_can_create_room_with_valid_data()
    {
        $response = $this->actingAs($this->owner)
            ->post(route('owner.rooms.store'), [
                'room_number' => '101',
                'room_type' => 'Standard',
                'standard_duration' => 60,
            ]);

        $response->assertRedirect(route('owner.rooms.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('rooms', [
            'hotel_id' => $this->hotel->id,
            'room_number' => '101',
            'room_type' => 'Standard',
            'standard_duration' => 60,
        ]);
    }

    /** @test */
    public function owner_cannot_create_room_with_duplicate_number()
    {
        Room::factory()->create([
            'hotel_id' => $this->hotel->id,
            'room_number' => '101',
        ]);

        $response = $this->actingAs($this->owner)
            ->post(route('owner.rooms.store'), [
                'room_number' => '101',
                'room_type' => 'Standard',
                'standard_duration' => 60,
            ]);

        $response->assertSessionHasErrors('room_number');
    }

    /** @test */
    public function owner_cannot_create_room_with_invalid_duration()
    {
        $response = $this->actingAs($this->owner)
            ->post(route('owner.rooms.store'), [
                'room_number' => '101',
                'room_type' => 'Standard',
                'standard_duration' => 0,
            ]);

        $response->assertSessionHasErrors('standard_duration');
    }

    /** @test */
    public function owner_can_update_room()
    {
        $room = Room::factory()->create(['hotel_id' => $this->hotel->id]);

        $response = $this->actingAs($this->owner)
            ->put(route('owner.rooms.update', $room), [
                'room_number' => '202',
                'room_type' => 'Deluxe',
                'standard_duration' => 90,
            ]);

        $response->assertRedirect(route('owner.rooms.index'));

        $this->assertDatabaseHas('rooms', [
            'id' => $room->id,
            'room_number' => '202',
            'room_type' => 'Deluxe',
            'standard_duration' => 90,
        ]);
    }

    /** @test */
    public function owner_can_delete_room_without_active_tasks()
    {
        $room = Room::factory()->create(['hotel_id' => $this->hotel->id]);

        $response = $this->actingAs($this->owner)
            ->delete(route('owner.rooms.destroy', $room));

        $response->assertRedirect(route('owner.rooms.index'));

        $this->assertDatabaseMissing('rooms', [
            'id' => $room->id,
        ]);
    }

    /** @test */
    public function owner_cannot_delete_room_with_active_tasks()
    {
        $room = Room::factory()->create(['hotel_id' => $this->hotel->id]);

        CleaningTask::factory()->create([
            'room_id' => $room->id,
            'status' => 'pending',
            'date' => today(),
        ]);

        $response = $this->actingAs($this->owner)
            ->delete(route('owner.rooms.destroy', $room));

        $response->assertSessionHas('error');

        $this->assertDatabaseHas('rooms', [
            'id' => $room->id,
        ]);
    }

    /** @test */
    public function owner_cannot_access_other_hotel_rooms()
    {
        $otherHotel = Hotel::factory()->create();
        $otherRoom = Room::factory()->create(['hotel_id' => $otherHotel->id]);

        $response = $this->actingAs($this->owner)
            ->get(route('owner.rooms.show', $otherRoom));

        $response->assertStatus(403);
    }
}
