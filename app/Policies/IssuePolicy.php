<?php

namespace App\Policies;

use App\Models\Issue;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class IssuePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->canManageHotel() || $user->isCleaner();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Issue $issue): bool
    {
        // Owner/authed user can view issues from their hotel
        if ($user->canManageHotel()) {
            return $issue->room->hotel_id === $user->hotel->id;
        }

        // Cleaner can view issues from their hotel
        if ($user->isCleaner()) {
            return $issue->room->hotel_id === $user->cleaner->hotel_id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Both owners and cleaners can create issues
        return $user->canManageHotel() || $user->isCleaner();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Issue $issue): bool
    {
        // Only owner/authed user can update (mark as fixed)
        if (!$user->canManageHotel()) {
            return false;
        }

        return $issue->room->hotel_id === $user->hotel->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Issue $issue): bool
    {
        // Only owner/authed user can delete
        if (!$user->canManageHotel()) {
            return false;
        }

        return $issue->room->hotel_id === $user->hotel->id;
    }
}
