<?php

namespace App\Modules\Emails\Policies;

use App\Models\User;
use App\Modules\Emails\Models\EmailLog;

class EmailLogPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('emails.logs.view');
    }

    public function view(User $user, EmailLog $emailLog): bool
    {
        return $user->can('emails.logs.view');
    }
}
