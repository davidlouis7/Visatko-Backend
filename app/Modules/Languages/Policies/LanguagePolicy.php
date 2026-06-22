<?php

namespace App\Modules\Languages\Policies;

use App\Models\User;
use App\Modules\Languages\Models\Language;

class LanguagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('languages.view');
    }

    public function create(User $user): bool
    {
        return $user->can('languages.create');
    }

    public function update(User $user, Language $language): bool
    {
        return $user->can('languages.update');
    }

    public function delete(User $user, Language $language): bool
    {
        return ! $language->is_default && $user->can('languages.delete');
    }
}
