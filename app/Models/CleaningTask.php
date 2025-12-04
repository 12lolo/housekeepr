<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CleaningTask extends Model
{
    use HasFactory;
    protected $fillable = [
        'room_id',
        'cleaner_id',
        'booking_id',
        'date',
        'deadline',
        'planned_duration',
        'suggested_start_time',
        'status',
        'actual_start_time',
        'actual_end_time',
        'actual_duration',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'deadline' => 'datetime',
            'suggested_start_time' => 'datetime',
            'actual_start_time' => 'datetime',
            'actual_end_time' => 'datetime',
        ];
    }

    // Relationships
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function cleaner()
    {
        return $this->belongsTo(Cleaner::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function taskLogs()
    {
        return $this->hasMany(TaskLog::class);
    }

    // Helper methods
    public function start()
    {
        $this->update([
            'status' => 'in_progress',
            'actual_start_time' => now(),
        ]);

        $this->taskLogs()->create([
            'action' => 'started',
            'timestamp' => now(),
        ]);
    }

    public function stop()
    {
        $this->taskLogs()->create([
            'action' => 'stopped',
            'timestamp' => now(),
        ]);
    }

    public function complete()
    {
        $actualDuration = null;
        if ($this->actual_start_time) {
            $actualDuration = now()->diffInMinutes($this->actual_start_time);
        }

        $this->update([
            'status' => 'completed',
            'actual_end_time' => now(),
            'actual_duration' => $actualDuration,
        ]);

        $this->taskLogs()->create([
            'action' => 'completed',
            'timestamp' => now(),
        ]);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}
