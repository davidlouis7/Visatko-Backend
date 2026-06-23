<?php

namespace App\Modules\Payments\Enums;

enum PaymentTransactionType: string
{
    case Payment = 'payment';
    case Refund = 'refund';
    case Authorization = 'authorization';
    case Capture = 'capture';
}
