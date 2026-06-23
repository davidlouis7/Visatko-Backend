<?php

namespace App\Modules\Team\Policies;

use App\Models\User;
use App\Modules\Team\Models\TeamMember;

class TeamMemberPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('team.view');
    }

    public function view(User $user, TeamMember $teamMember): bool
    {
        return $user->can('team.view');
    }

    public function create(User $user): bool
    {
        return $user->can('team.create');
    }

    public function update(User $user, TeamMember $teamMember): bool
    {
        return $user->can('team.update');
    }

    public function delete(User $user, TeamMember $teamMember): bool
    {
        return $user->can('team.delete');
    }
}
