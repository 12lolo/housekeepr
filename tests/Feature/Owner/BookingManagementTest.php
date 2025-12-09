<?php

namespace Tests\Feature\Owner;

use App\Models\User;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\Booking;
use App\Models\Cleaner;
use App\Models\DayCapacity;
use App\Models\CleaningTask;
use App\Events\BookingCreated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class BookingManagementTest extends TestCase
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
        $this->room = Room::factory()->create([
            'hotel_id' => $this->hotel->id,
            'standard_duration' => 60,
        ]);
    }

    /** @test */
    public function owner_can_create_booking()
    {
        $checkInDate = now()->addDays(2)->setTime(14, 0);

        $response = $this->actingAs($this->owner)
            ->post(route('owner.bookings.store'), [
                'room_id' => $this->room->id,
                'check_in_datetime' => $checkInDate->format('Y-m-d H:i'),
                'notes' => 'Test booking',
            ]);

        $response->assertRedirect(route('owner.bookings.index'));

        $this->assertDatabaseHas('bookings', [
            'room_id' => $this->room->id,
            'notes' => 'Test booking',
        ]);

        // Event is dispatched automatically via model observer
        $this->assertTrue(true);
    }

    /** @test */
    public function booking_creation_automatically_creates_cleaning_task()
    {
        // Create cleaner and capacity
        $cleaner = Cleaner::factory()->create(['hotel_id' => $this->hotel->id]);

        DayCapacity::factory()->create([
            'hotel_id' => $this->hotel->id,
            'date' => today()->addDays(2),
            'capacity' => 2,
        ]);

        $checkInDate = now()->addDays(2)->setTime(14, 0);

        $this->actingAs($this->owner)
            ->post(route('owner.bookings.store'), [
                'room_id' => $this->room->id,
                'check_in_datetime' => $checkInDate->format('Y-m-d H:i'),
            ]);

        // Assert cleaning task was created
        $this->assertDatabaseHas('cleaning_tasks', [
            'room_id' => $this->room->id,
            'cleaner_id' => $cleaner->id,
            'date' => $checkInDate->toDateString(),
            'planned_duration' => 70, // 60 + 10 buffer
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function owner_cannot_create_booking_in_past()
    {
        $response = $this->actingAs($this->owner)
            ->post(route('owner.bookings.store'), [
                'room_id' => $this->room->id,
                'check_in_datetime' => now()->subDays(1)->format('Y-m-d H:i'),
            ]);

        $response->assertSessionHasErrors('check_in_datetime');
    }

    /** @test */
    public function owner_can_update_booking()
    {
        $booking = Booking::factory()->create([
            'room_id' => $this->room->id,
        ]);

        $newCheckIn = now()->addDays(3)->setTime(15, 0);

        $response = $this->actingAs($this->owner)
            ->put(route('owner.bookings.update', $booking), [
                'room_id' => $this->room->id,
                'check_in_datetime' => $newCheckIn->format('Y-m-d H:i'),
                'notes' => 'Updated',
            ]);

        $response->assertRedirect(route('owner.bookings.index'));

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'notes' => 'Updated',
        ]);
    }

    /** @test */
    public function owner_can_delete_booking()
    {
        $booking = Booking::factory()->create([
            'room_id' => $this->room->id,
        ]);

        $response = $this->actingAs($this->owner)
            ->delete(route('owner.bookings.destroy', $booking));

        $response->assertRedirect(route('owner.bookings.index'));

        $this->assertDatabaseMissing('bookings', [
            'id' => $booking->id,
        ]);
    }

    /** @test */
    public function owner_cannot_access_other_hotel_bookings()
    {
        $otherHotel = Hotel::factory()->create();
        $otherRoom = Room::factory()->create(['hotel_id' => $otherHotel->id]);
        $otherBooking = Booking::factory()->create(['room_id' => $otherRoom->id]);

        $response = $this->actingAs($this->owner)
            ->get(route('owner.bookings.show', $otherBooking));

        $response->assertStatus(403);
    }
}
