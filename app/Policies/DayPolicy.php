<?php

namespace App\Policies;

use App\Models\Day;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DayPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Day $day): bool
    {
        return $user->id === $day->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Day $day): bool
    {
        return $user->id === $day->user_id;
    }

    public function delete(User $user, Day $day): bool
    {
        return $user->id === $day->user_id;
    }
}
