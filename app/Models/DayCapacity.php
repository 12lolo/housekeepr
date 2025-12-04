<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DayCapacity extends Model
{
    use HasFactory;
    protected $fillable = [
        'hotel_id',
        'date',
        'capacity',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    // Relationships
    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
}
