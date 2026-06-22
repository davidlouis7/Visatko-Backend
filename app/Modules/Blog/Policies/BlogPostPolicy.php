<?php

namespace App\Modules\Blog\Policies;

use App\Models\User;
use App\Modules\Blog\Models\BlogPost;

class BlogPostPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('blog.view');
    }

    public function create(User $user): bool
    {
        return $user->can('blog.create');
    }

    public function update(User $user, BlogPost $post): bool
    {
        return $user->can('blog.update');
    }

    public function delete(User $user, BlogPost $post): bool
    {
        return $user->can('blog.delete');
    }
}
