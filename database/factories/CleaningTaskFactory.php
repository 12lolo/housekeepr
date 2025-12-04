<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\Cleaner;
use Illuminate\Database\Eloquent\Factories\Factory;

class CleaningTaskFactory extends Factory
{
    public function definition(): array
    {
        $date = fake()->dateTimeBetween('now', '+7 days');

        return [
            'room_id' => Room::factory(),
            'cleaner_id' => Cleaner::factory(),
            'date' => $date->format('Y-m-d'),
            'deadline' => $date->setTime(14, 0),
            'planned_duration' => 60,
            'suggested_start_time' => (clone $date)->setTime(13, 0),
            'status' => 'pending',
        ];
    }
}
