<?php

namespace Database\Factories;

use App\Models\Hotel;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{
    public function definition(): array
    {
        return [
            'hotel_id' => Hotel::factory(),
            'room_number' => fake()->unique()->numerify('###'),
            'room_type' => fake()->randomElement(['Standard', 'Deluxe', 'Suite']),
            'standard_duration' => fake()->randomElement([30, 45, 60, 90]),
        ];
    }
}
