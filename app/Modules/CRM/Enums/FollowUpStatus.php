<?php

namespace App\Modules\CRM\Enums;

enum FollowUpStatus: string
{
    case Pending = 'pending';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Overdue = 'overdue';
}
