<?php

namespace App\Modules\Emails\Enums;

enum EmailLogStatus: string
{
    case Queued = 'queued';
    case Sent = 'sent';
    case Failed = 'failed';
}
