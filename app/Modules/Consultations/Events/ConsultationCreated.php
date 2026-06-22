<?php

namespace App\Modules\Consultations\Events;

use App\Modules\Consultations\Models\Consultation;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConsultationCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public Consultation $consultation) {}
}
