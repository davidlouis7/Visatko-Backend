<?php

namespace App\Modules\Media\Policies;

use App\Models\User;
use App\Modules\Media\Models\Media;

class MediaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('media.view');
    }

    public function view(User $user, Media $media): bool
    {
        return $media->visibility === 'public' || $user->can('media.view');
    }

    public function delete(User $user, Media $media): bool
    {
        return $user->can('media.delete');
    }
}
