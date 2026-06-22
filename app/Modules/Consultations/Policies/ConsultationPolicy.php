<?php

namespace App\Modules\Consultations\Policies;

use App\Models\User;
use App\Modules\Consultations\Models\Consultation;

class ConsultationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('consultations.view');
    }

    public function view(User $user, Consultation $consultation): bool
    {
        return $user->can('consultations.view');
    }

    public function update(User $user, Consultation $consultation): bool
    {
        return $user->can('consultations.update');
    }

    public function delete(User $user, Consultation $consultation): bool
    {
        return $user->can('consultations.delete');
    }
}
