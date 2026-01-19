<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Cleaner extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'hotel_id',
        'status',
    ];

    protected $casts = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // Only add availability columns if they exist in database
        if (Schema::hasColumn('cleaners', 'works_monday')) {
            $this->fillable = array_merge($this->fillable, [
                'works_monday',
                'works_tuesday',
                'works_wednesday',
                'works_thursday',
                'works_friday',
                'works_saturday',
                'works_sunday',
            ]);

            $this->casts = [
                'works_monday' => 'boolean',
                'works_tuesday' => 'boolean',
                'works_wednesday' => 'boolean',
                'works_thursday' => 'boolean',
                'works_friday' => 'boolean',
                'works_saturday' => 'boolean',
                'works_sunday' => 'boolean',
            ];
        }
    }

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

    /**
     * Check if cleaner is available on a specific day of week
     *
     * @param  int  $dayOfWeek  0=Sunday, 1=Monday, ..., 6=Saturday
     */
    public function isAvailableOnDay(int $dayOfWeek): bool
    {
        // Fallback for backward compatibility if columns don't exist yet
        if (! Schema::hasColumn('cleaners', 'works_monday')) {
            return true; // Default to available if migration hasn't run
        }

        return match ($dayOfWeek) {
            0 => $this->works_sunday ?? false,
            1 => $this->works_monday ?? true,
            2 => $this->works_tuesday ?? true,
            3 => $this->works_wednesday ?? true,
            4 => $this->works_thursday ?? true,
            5 => $this->works_friday ?? true,
            6 => $this->works_saturday ?? false,
            default => false,
        };
    }

    /**
     * Get array of available days (0-6)
     */
    public function getAvailableDays(): array
    {
        if (! Schema::hasColumn('cleaners', 'works_monday')) {
            return [1, 2, 3, 4, 5]; // Default weekdays if columns don't exist
        }

        $days = [];
        if ($this->works_sunday ?? false) {
            $days[] = 0;
        }
        if ($this->works_monday ?? true) {
            $days[] = 1;
        }
        if ($this->works_tuesday ?? true) {
            $days[] = 2;
        }
        if ($this->works_wednesday ?? true) {
            $days[] = 3;
        }
        if ($this->works_thursday ?? true) {
            $days[] = 4;
        }
        if ($this->works_friday ?? true) {
            $days[] = 5;
        }
        if ($this->works_saturday ?? false) {
            $days[] = 6;
        }

        return $days;
    }

    /**
     * Get human-readable list of working days
     */
    public function getWorkingDaysText(): string
    {
        if (! Schema::hasColumn('cleaners', 'works_monday')) {
            return 'Ma-Vr'; // Default if columns don't exist
        }

        $days = [];
        if ($this->works_monday ?? true) {
            $days[] = 'Ma';
        }
        if ($this->works_tuesday ?? true) {
            $days[] = 'Di';
        }
        if ($this->works_wednesday ?? true) {
            $days[] = 'Wo';
        }
        if ($this->works_thursday ?? true) {
            $days[] = 'Do';
        }
        if ($this->works_friday ?? true) {
            $days[] = 'Vr';
        }
        if ($this->works_saturday ?? false) {
            $days[] = 'Za';
        }
        if ($this->works_sunday ?? false) {
            $days[] = 'Zo';
        }

        return implode(', ', $days);
    }
}
