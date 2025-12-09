<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'owner_id',
        'address',
        'street',
        'house_number',
        'house_number_addition',
        'postal_code',
        'city',
        'country',
    ];

    // Relationships
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function cleaners()
    {
        return $this->hasMany(Cleaner::class);
    }

    public function dayCapacities()
    {
        return $this->hasMany(DayCapacity::class);
    }
}
