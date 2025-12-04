<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;
    protected $fillable = [
        'hotel_id',
        'room_number',
        'room_type',
        'standard_duration',
    ];

    // Relationships
    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function cleaningTasks()
    {
        return $this->hasMany(CleaningTask::class);
    }

    public function issues()
    {
        return $this->hasMany(Issue::class);
    }

    // Helper method to check if room has blocking issues
    public function hasBlockingIssue(): bool
    {
        return $this->issues()
            ->where('status', 'open')
            ->where('impact', 'kan_niet_gebruikt')
            ->exists();
    }
}
