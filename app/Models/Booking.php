<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;
    protected $fillable = [
        'room_id',
        'check_in_datetime',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'check_in_datetime' => 'datetime',
        ];
    }

    // Relationships
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function cleaningTask()
    {
        return $this->hasOne(CleaningTask::class);
    }

    // Trigger event when booking is created to auto-create cleaning task
    protected static function booted()
    {
        static::created(function ($booking) {
            event(new \App\Events\BookingCreated($booking));
        });
    }
}
