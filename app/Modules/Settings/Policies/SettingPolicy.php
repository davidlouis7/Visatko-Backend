<?php

namespace App\Modules\Settings\Policies;

use App\Models\User;
use App\Modules\Settings\Models\Setting;

class SettingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('settings.view');
    }

    public function create(User $user): bool
    {
        return $user->can('settings.create');
    }

    public function update(User $user, Setting $setting): bool
    {
        return $user->can('settings.update');
    }

    public function delete(User $user, Setting $setting): bool
    {
        return $user->can('settings.delete');
    }
}
