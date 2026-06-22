<?php

namespace App\Modules\Consultations\Actions;

use App\Models\User;
use App\Modules\Consultations\Enums\ConsultationStatus;
use App\Modules\Consultations\Models\Consultation;
use App\Modules\CRM\Actions\AddTimelineEntry;
use App\Modules\VisaApplications\Actions\CreateVisaApplicationAction;
use App\Modules\VisaApplications\Models\VisaApplication;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ConvertConsultationToApplicationAction
{
    public function __construct(private CreateVisaApplicationAction $createApplication, private AddTimelineEntry $timeline) {}

    public function execute(Consultation $consultation, int $serviceId, User $actor): VisaApplication
    {
        if ($consultation->converted_application_id) {
            throw ValidationException::withMessages(['consultation' => ['This consultation has already been converted.']]);
        }

        return DB::transaction(function () use ($consultation, $serviceId, $actor): VisaApplication {
            $application = $this->createApplication->execute(['visa_service_id' => $serviceId, 'full_name' => $consultation->full_name, 'email' => $consultation->email, 'phone' => $consultation->phone, 'whatsapp_number' => $consultation->whatsapp_number, 'nationality' => $consultation->nationality, 'emirate' => $consultation->current_emirate, 'travel_date' => $consultation->expected_travel_date?->format('Y-m-d'), 'customer_notes' => $consultation->notes, 'source' => $consultation->source], $consultation->id);
            $consultation->update(['converted_application_id' => $application->id, 'status' => ConsultationStatus::ConvertedToApplication]);
            $this->timeline->execute($consultation, 'consultation_converted', 'Consultation converted to application', $actor, null, null, ['application_id' => $application->id]);
            activity('admin')->causedBy($actor)->performedOn($consultation)->log('Consultation converted');

            return $application;
        });
    }
}
