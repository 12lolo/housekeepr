<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasRoles, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
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
}
