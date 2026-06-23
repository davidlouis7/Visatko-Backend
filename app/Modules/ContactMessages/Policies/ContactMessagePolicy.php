<?php

namespace App\Modules\ContactMessages\Policies;

use App\Models\User;
use App\Modules\ContactMessages\Models\ContactMessage;

class ContactMessagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('contact_messages.view');
    }

    public function view(User $user, ContactMessage $message): bool
    {
        return $user->can('contact_messages.view');
    }

    public function update(User $user, ContactMessage $message): bool
    {
        return $user->can('contact_messages.update');
    }
}
