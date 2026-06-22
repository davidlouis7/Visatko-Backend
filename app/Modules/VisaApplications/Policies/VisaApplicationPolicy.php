<?php

namespace App\Modules\VisaApplications\Policies;

use App\Models\User;
use App\Modules\VisaApplications\Models\VisaApplication;

class VisaApplicationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('applications.view');
    }

    public function view(User $user, VisaApplication $application): bool
    {
        return $user->can('applications.view');
    }

    public function update(User $user, VisaApplication $application): bool
    {
        return $user->can('applications.update');
    }

    public function delete(User $user, VisaApplication $application): bool
    {
        return $user->can('applications.delete');
    }
}
