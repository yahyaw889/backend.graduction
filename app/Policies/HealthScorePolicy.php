<?php

namespace App\Policies;

use App\Models\HealthScore;
use App\Models\User;

class HealthScorePolicy
{
    public function view(User $user, HealthScore $healthScore): bool
    {
        return $user->id === $healthScore->user_id;
    }

    public function update(User $user, HealthScore $healthScore): bool
    {
        return $user->id === $healthScore->user_id;
    }

    public function delete(User $user, HealthScore $healthScore): bool
    {
        return $user->id === $healthScore->user_id;
    }
}
