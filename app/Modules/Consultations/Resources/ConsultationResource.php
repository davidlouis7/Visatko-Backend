<?php

namespace App\Modules\Consultations\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConsultationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'public_id' => $this->public_id,
            'customer_id' => $this->customer_id,
            'status' => $this->status->value,
            'full_name' => $this->full_name,
            'phone' => $this->phone, 'whatsapp_number' => $this->whatsapp_number, 'email' => $this->email,
            'nationality' => $this->nationality, 'current_emirate' => $this->current_emirate,
            'preferred_destination_country_id' => $this->preferred_destination_country_id,
            'preferred_visa_service_id' => $this->preferred_visa_service_id,
            'are_you_residing_in_uae' => $this->are_you_residing_in_uae,
            'monthly_salary_range' => $this->monthly_salary_range,
            'salary_transferred_regularly' => $this->salary_transferred_regularly,
            'has_tenancy_contract' => $this->has_tenancy_contract, 'owns_car' => $this->owns_car,
            'has_previous_travel_history' => $this->has_previous_travel_history, 'previous_visa_refusal' => $this->previous_visa_refusal,
            'expected_travel_date' => $this->expected_travel_date?->toDateString(), 'notes' => $this->notes, 'source' => $this->source,
            'assigned_to' => $this->assigned_to, 'converted_application_id' => $this->converted_application_id,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
