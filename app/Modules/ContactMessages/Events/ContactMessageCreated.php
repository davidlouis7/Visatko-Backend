<?php

namespace App\Modules\ContactMessages\Events;

use App\Modules\ContactMessages\Models\ContactMessage;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContactMessageCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public ContactMessage $contactMessage) {}
}
