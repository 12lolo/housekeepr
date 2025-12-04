<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'room_id' => Room::factory(),
            'check_in_datetime' => fake()->dateTimeBetween('+1 day', '+14 days')->setTime(14, 0),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
