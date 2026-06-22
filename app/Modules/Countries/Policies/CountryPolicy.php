<?php

namespace App\Modules\Countries\Policies;

use App\Models\User;
use App\Modules\Countries\Models\Country;

class CountryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('countries.view');
    }

    public function create(User $user): bool
    {
        return $user->can('countries.create');
    }

    public function update(User $user, Country $country): bool
    {
        return $user->can('countries.update');
    }

    public function delete(User $user, Country $country): bool
    {
        return $user->can('countries.delete') && ! $country->services()->exists();
    }
}
