<?php

namespace App\Modules\Marketing\Enums;

enum MarketingEventStatus: string
{
    case Pending = 'pending';
    case Sent = 'sent';
    case Failed = 'failed';
    case Skipped = 'skipped';
}
