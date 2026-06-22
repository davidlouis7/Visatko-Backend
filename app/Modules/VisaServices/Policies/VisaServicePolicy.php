<?php

namespace App\Modules\VisaServices\Policies;

use App\Models\User;
use App\Modules\VisaServices\Models\VisaService;

class VisaServicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('services.view');
    }

    public function create(User $user): bool
    {
        return $user->can('services.create');
    }

    public function update(User $user, VisaService $service): bool
    {
        return $user->can('services.update');
    }

    public function delete(User $user, VisaService $service): bool
    {
        return $user->can('services.delete');
    }
}
