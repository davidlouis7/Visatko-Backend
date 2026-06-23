<?php

namespace App\Modules\Payments\Events;

use App\Modules\Payments\Models\PaymentTransaction;

class BankTransferRejected
{
    public function __construct(public PaymentTransaction $transaction) {}
}
