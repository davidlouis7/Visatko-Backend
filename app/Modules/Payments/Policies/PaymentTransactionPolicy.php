<?php

namespace App\Modules\Payments\Policies;

use App\Models\User;
use App\Modules\Payments\Models\PaymentTransaction;

class PaymentTransactionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('payments.view');
    }

    public function view(User $user, PaymentTransaction $transaction): bool
    {
        return $user->can('payments.view');
    }
}
