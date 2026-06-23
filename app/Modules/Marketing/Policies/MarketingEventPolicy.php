<?php

namespace App\Modules\Marketing\Policies;

use App\Models\User;
use App\Modules\Marketing\Models\MarketingEvent;

class MarketingEventPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('marketing.events.view');
    }

    public function view(User $user, MarketingEvent $event): bool
    {
        return $user->can('marketing.events.view');
    }
}
