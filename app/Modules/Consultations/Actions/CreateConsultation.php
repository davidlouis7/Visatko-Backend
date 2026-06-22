<?php

namespace App\Modules\Consultations\Actions;

use App\Modules\Consultations\Enums\ConsultationStatus;
use App\Modules\Consultations\Events\ConsultationCreated;
use App\Modules\Consultations\Models\Consultation;
use App\Modules\CRM\Actions\AddTimelineEntry;
use App\Modules\Customers\Actions\ResolveCustomer;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CreateConsultation
{
    public function __construct(private ResolveCustomer $customers, private AddTimelineEntry $timeline) {}

    public function execute(array $attributes): Consultation
    {
        $consultation = DB::transaction(function () use ($attributes): Consultation {
            $customer = $this->customers->execute($attributes);
            $consultation = Consultation::query()->create([...Arr::except($attributes, ['preferred_language']), 'customer_id' => $customer->id, 'status' => ConsultationStatus::New, 'source' => $attributes['source'] ?? 'website']);
            $this->timeline->execute($consultation, 'consultation_created', 'Consultation created');

            return $consultation;
        });
        event(new ConsultationCreated($consultation));

        return $consultation;
    }
}
