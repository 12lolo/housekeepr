<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    protected $fillable = [
        'room_id',
        'reported_by',
        'impact',
        'note',
        'photo_path',
        'status',
    ];

    // Relationships
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    // Alias for backwards compatibility (used in views)
    public function reportedBy()
    {
        return $this->reporter();
    }

    // Helper methods
    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isFixed(): bool
    {
        return $this->status === 'gefixt';
    }

    public function isBlocking(): bool
    {
        return $this->impact === 'kan_niet_gebruikt';
    }

    public function markAsFixed()
    {
        $this->update(['status' => 'gefixt']);

        // Trigger event for potential replanning
        event(new \App\Events\IssueFixed($this));
    }
}
