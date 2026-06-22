<?php

namespace App\Modules\Blog\Policies;

use App\Models\User;
use App\Modules\Blog\Models\BlogTag;

class BlogTagPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('blog.view');
    }

    public function create(User $user): bool
    {
        return $user->can('blog.create');
    }

    public function update(User $user, BlogTag $tag): bool
    {
        return $user->can('blog.update');
    }

    public function delete(User $user, BlogTag $tag): bool
    {
        return $user->can('blog.delete');
    }
}
