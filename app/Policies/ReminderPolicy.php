<?php

namespace App\Policies;

use App\Models\Reminder;
use App\Models\User;

class ReminderPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Reminder $reminder): bool
    {
        return $user->id === $reminder->user_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Reminder $reminder): bool
    {
        return $user->id === $reminder->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Reminder $reminder): bool
    {
        return $user->id === $reminder->user_id;
    }
}
