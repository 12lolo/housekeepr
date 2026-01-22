<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'status',
        'notifications_enabled',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'notifications_enabled' => 'boolean',
        ];
    }

    // Relationships
    public function hotel()
    {
        return $this->hasOne(Hotel::class, 'owner_id');
    }

    public function hotels()
    {
        return $this->hasMany(Hotel::class, 'owner_id');
    }

    public function cleaner()
    {
        return $this->hasOne(Cleaner::class);
    }

    public function reportedIssues()
    {
        return $this->hasMany(Issue::class, 'reported_by');
    }

    // Helper methods for role checking
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function isAuthedUser(): bool
    {
        return $this->role === 'authed-user';
    }

    public function isCleaner(): bool
    {
        return $this->role === 'cleaner';
    }

    public function canManageHotel(): bool
    {
        return in_array($this->role, ['owner', 'authed-user']);
    }

    /**
     * Get the hotel associated with this user (for owners and cleaners).
     * Returns null if user has no associated hotel.
     */
    public function getHotel(): ?Hotel
    {
        if ($this->isOwner() || $this->isAuthedUser()) {
            return $this->hotel;
        }

        if ($this->isCleaner() && $this->cleaner) {
            return $this->cleaner->hotel;
        }

        return null;
    }

    // Status checking methods
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isDeactivated(): bool
    {
        return $this->status === 'deactivated';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function canPerformActions(): bool
    {
        // Only active users can perform actions (create, edit, delete)
        // Deactivated users can only view (navigate dashboard)
        return $this->status === 'active';
    }

    /**
     * Get the system user ID for automated operations.
     * Returns 1 if exists, otherwise null.
     */
    public static function getSystemUserId(): ?int
    {
        $systemUser = static::where('role', 'admin')->first();

        return $systemUser?->id ?? 1; // Fallback to ID 1 if no admin exists
    }
}
