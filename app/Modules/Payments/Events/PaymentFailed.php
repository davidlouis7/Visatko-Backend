<?php

namespace App\Modules\Payments\Events;

use App\Modules\Payments\Models\PaymentTransaction;

class PaymentFailed
{
    public function __construct(public PaymentTransaction $transaction) {}
}
