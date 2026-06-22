<?php

namespace App\Modules\Customers\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'full_name' => $this->full_name, 'email' => $this->email, 'phone' => $this->phone, 'whatsapp_number' => $this->whatsapp_number, 'nationality' => $this->nationality, 'residence_country' => $this->residence_country, 'emirate' => $this->emirate, 'preferred_language' => $this->preferred_language, 'source' => $this->source, 'notes' => $this->notes, 'is_active' => $this->is_active, 'consultations_count' => $this->whenCounted('consultations'), 'visa_applications_count' => $this->whenCounted('visaApplications'), 'created_at' => $this->created_at?->toISOString()];
    }
}
