<?php

namespace App\Policies;

use App\Models\CleaningTask;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CleaningTaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Owners/authed users can view all tasks, cleaners can view their own
        return $user->canManageHotel() || $user->isCleaner();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CleaningTask $cleaningTask): bool
    {
        // Owner/authed user can view tasks from their hotel
        if ($user->canManageHotel()) {
            return $cleaningTask->room->hotel_id === $user->hotel->id;
        }

        // Cleaner can only view their own tasks
        if ($user->isCleaner()) {
            return $cleaningTask->cleaner->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->canManageHotel();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CleaningTask $cleaningTask): bool
    {
        // Owner/authed user can update tasks from their hotel
        if ($user->canManageHotel()) {
            return $cleaningTask->room->hotel_id === $user->hotel->id;
        }

        // Cleaner can only update their own tasks (start/stop/complete)
        if ($user->isCleaner()) {
            return $cleaningTask->cleaner->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CleaningTask $cleaningTask): bool
    {
        // Only owner/authed user can delete
        if (!$user->canManageHotel()) {
            return false;
        }

        return $cleaningTask->room->hotel_id === $user->hotel->id;
    }
}
