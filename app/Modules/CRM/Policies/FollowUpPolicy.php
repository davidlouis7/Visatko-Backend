<?php

namespace App\Modules\CRM\Policies;

use App\Models\User;
use App\Modules\CRM\Models\FollowUp;

class FollowUpPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('follow_ups.view');
    }

    public function create(User $user): bool
    {
        return $user->can('follow_ups.create');
    }

    public function update(User $user, FollowUp $followUp): bool
    {
        return $user->can('follow_ups.update');
    }
}
