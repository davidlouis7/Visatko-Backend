<?php

namespace App\Modules\Refunds\Enums;

enum RefundRequestStatus: string
{
    case Requested = 'requested';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Processed = 'processed';
    case Cancelled = 'cancelled';
}
