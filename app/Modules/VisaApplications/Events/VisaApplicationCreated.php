<?php

namespace App\Modules\VisaApplications\Events;

use App\Modules\VisaApplications\Models\VisaApplication;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VisaApplicationCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public VisaApplication $application) {}
}
