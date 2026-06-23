<?php

namespace App\Modules\Emails\Policies;

use App\Models\User;
use App\Modules\Emails\Models\EmailTemplate;

class EmailTemplatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('emails.templates.view');
    }

    public function view(User $user, EmailTemplate $template): bool
    {
        return $user->can('emails.templates.view');
    }

    public function update(User $user, EmailTemplate $template): bool
    {
        return $user->can('emails.templates.update');
    }
}
