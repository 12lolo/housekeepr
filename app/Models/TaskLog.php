<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskLog extends Model
{
    public $timestamps = false; // Using custom timestamp field

    protected $fillable = [
        'cleaning_task_id',
        'action',
        'timestamp',
    ];

    protected function casts(): array
    {
        return [
            'timestamp' => 'datetime',
        ];
    }

    // Relationships
    public function cleaningTask()
    {
        return $this->belongsTo(CleaningTask::class);
    }
}
