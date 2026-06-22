<?php

namespace App\Modules\Pages\Policies;

use App\Models\User;
use App\Modules\Pages\Models\Page;

class PagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('pages.view');
    }

    public function create(User $user): bool
    {
        return $user->can('pages.create');
    }

    public function update(User $user, Page $page): bool
    {
        return $user->can('pages.update');
    }

    public function delete(User $user, Page $page): bool
    {
        return $user->can('pages.delete');
    }
}
