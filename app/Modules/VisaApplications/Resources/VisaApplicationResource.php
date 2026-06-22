<?php

namespace App\Modules\VisaApplications\Resources;

use App\Modules\VisaServices\Resources\VisaServiceResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VisaApplicationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'application_number' => $this->application_number, 'customer_id' => $this->customer_id, 'visa_service_id' => $this->visa_service_id, 'visa_service' => VisaServiceResource::make($this->whenLoaded('visaService')), 'consultation_id' => $this->consultation_id, 'assigned_to' => $this->assigned_to, 'full_name' => $this->full_name, 'email' => $this->email, 'phone' => $this->phone, 'whatsapp_number' => $this->whatsapp_number, 'nationality' => $this->nationality, 'residence_country' => $this->residence_country, 'emirate' => $this->emirate, 'passport_number' => $this->passport_number, 'travel_date' => $this->travel_date?->toDateString(), 'status' => $this->status->value, 'payment_status' => $this->payment_status->value, 'customer_notes' => $this->customer_notes, 'internal_notes' => $this->when($request->is('api/v1/admin/*'), $this->internal_notes), 'source' => $this->source, 'submitted_at' => $this->submitted_at?->toISOString(), 'completed_at' => $this->completed_at?->toISOString(), 'created_at' => $this->created_at?->toISOString()];
    }
}
