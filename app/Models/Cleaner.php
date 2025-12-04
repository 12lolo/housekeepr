<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cleaner extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'hotel_id',
        'status',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function cleaningTasks()
    {
        return $this->hasMany(CleaningTask::class);
    }

    // Helper methods
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function tasksForDate($date)
    {
        return $this->cleaningTasks()
            ->where('date', $date)
            ->with('room', 'booking')
            ->orderBy('suggested_start_time')
            ->get();
    }
}
