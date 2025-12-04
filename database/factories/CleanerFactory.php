<?php

namespace Database\Factories;

use App\Models\Hotel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CleanerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->create(['role' => 'cleaner']),
            'hotel_id' => Hotel::factory(),
            'status' => 'active',
        ];
    }
}
