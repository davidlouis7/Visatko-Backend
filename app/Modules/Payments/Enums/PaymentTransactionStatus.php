<?php

namespace App\Modules\Payments\Enums;

enum PaymentTransactionStatus: string
{
    case Pending = 'pending';
    case Authorized = 'authorized';
    case Paid = 'paid';
    case Failed = 'failed';
    case Cancelled = 'cancelled';
    case Refunded = 'refunded';
    case PartiallyRefunded = 'partially_refunded';
    case PendingReview = 'pending_review';
}
