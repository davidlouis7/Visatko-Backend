<?php

namespace App\Modules\ApplicationDocuments\Events;

use App\Modules\ApplicationDocuments\Models\ApplicationDocument;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentRejected
{
    use Dispatchable, SerializesModels;

    public function __construct(public ApplicationDocument $document) {}
}
