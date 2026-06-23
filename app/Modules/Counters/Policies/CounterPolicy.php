<?php

namespace App\Modules\Counters\Policies;

use App\Models\User;
use App\Modules\Counters\Models\Counter;

class CounterPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('counters.view');
    }

    public function view(User $user, Counter $counter): bool
    {
        return $user->can('counters.view');
    }

    public function create(User $user): bool
    {
        return $user->can('counters.create');
    }

    public function update(User $user, Counter $counter): bool
    {
        return $user->can('counters.update');
    }

    public function delete(User $user, Counter $counter): bool
    {
        return $user->can('counters.delete');
    }
}
