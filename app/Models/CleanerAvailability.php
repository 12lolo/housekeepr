<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CleanerAvailability extends Model
{
    protected $table = 'cleaner_availability';

    protected $fillable = [
        'cleaner_id',
        'day_of_week',
    ];

    // Relationships
    public function cleaner()
    {
        return $this->belongsTo(Cleaner::class);
    }
}
