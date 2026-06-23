<?php

namespace App\Modules\Refunds\Policies;

use App\Models\User;
use App\Modules\Refunds\Models\RefundRequest;

class RefundRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('refunds.view');
    }

    public function view(User $user, RefundRequest $refundRequest): bool
    {
        return $user->can('refunds.view');
    }
}
