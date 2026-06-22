<?php

namespace App\Modules\ApplicationDocuments\Enums;

enum DocumentStatus: string
{
    case Uploaded = 'uploaded';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case NeedsReupload = 'needs_reupload';
}
