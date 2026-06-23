<?php

namespace App\Modules\Payments\Events;

use App\Modules\Payments\Models\PaymentTransaction;

class PaymentSucceeded
{
    public function __construct(public PaymentTransaction $transaction) {}
}
