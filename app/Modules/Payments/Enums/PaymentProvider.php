<?php

namespace App\Modules\Payments\Enums;

enum PaymentProvider: string
{
    case Stripe = 'stripe';
    case Tabby = 'tabby';
    case BankTransfer = 'bank_transfer';
    case Manual = 'manual';
}
