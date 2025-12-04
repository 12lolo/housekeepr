<?php

namespace App\Events;

use App\Models\DayCapacity;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CapacityUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $capacity;

    public function __construct(DayCapacity $capacity)
    {
        $this->capacity = $capacity;
    }
}
