<?php

namespace App\Modules\Payments\Events;

use App\Modules\Payments\Models\PaymentTransaction;

class BankTransferApproved
{
    public function __construct(public PaymentTransaction $transaction) {}
}
