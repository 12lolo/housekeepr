<?php

namespace Tests\Feature\Api;

use App\Models\Booking;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BookingApiTest extends TestCase
{
    use RefreshDatabase;

    protected $owner;
    protected $hotel;
    protected $room;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create(['role' => 'owner', 'status' => 'active']);
        $this->hotel = Hotel::factory()->create(['owner_id' => $this->owner->id]);
        $this->room = Room::factory()->create(['hotel_id' => $this->hotel->id]);
    }

    public function test_can_login_and_get_token(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => $this->owner->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user',
                'token',
                'token_type'
            ]);
    }

    public function test_can_get_bookings_list(): void
    {
        Sanctum::actingAs($this->owner);

        Booking::factory()->create([
            'room_id' => $this->room->id,
            'check_in_datetime' => now()->addDays(2),
        ]);

        $response = $this->getJson('/api/bookings');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'room_id', 'check_in_datetime', 'room']
                ]
            ]);
    }

    public function test_can_create_booking_via_api(): void
    {
        Sanctum::actingAs($this->owner);

        $response = $this->postJson('/api/bookings', [
            'room_id' => $this->room->id,
            'check_in_datetime' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'notes' => 'API test booking',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'booking' => ['id', 'room_id', 'check_in_datetime']
            ]);

        $this->assertDatabaseHas('bookings', [
            'room_id' => $this->room->id,
            'notes' => 'API test booking',
        ]);
    }

    public function test_cannot_access_api_without_token(): void
    {
        $response = $this->getJson('/api/bookings');

        $response->assertStatus(401);
    }

    public function test_can_logout(): void
    {
        Sanctum::actingAs($this->owner);

        $response = $this->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Logged out successfully']);
    }
}

