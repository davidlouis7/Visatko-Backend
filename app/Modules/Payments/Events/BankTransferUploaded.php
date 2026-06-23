<?php

namespace App\Modules\Payments\Events;

use App\Modules\Payments\Models\PaymentTransaction;

class BankTransferUploaded
{
    public function __construct(public PaymentTransaction $transaction) {}
}
