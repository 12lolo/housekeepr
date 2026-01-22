<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    public function definition(): array
    {
        $checkIn = fake()->dateTimeBetween('+1 day', '+14 days')->setTime(14, 0);
        $checkOut = (clone $checkIn)->modify('+'.fake()->numberBetween(1, 7).' days')->setTime(11, 0);

        return [
            'room_id' => Room::factory(),
            'guest_name' => fake()->name(),
            'check_in' => $checkIn->format('Y-m-d'),
            'check_out' => $checkOut->format('Y-m-d'),
            'check_in_datetime' => $checkIn,
            'check_out_datetime' => $checkOut,
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
