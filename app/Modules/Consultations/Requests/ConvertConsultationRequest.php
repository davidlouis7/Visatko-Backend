<?php

namespace App\Modules\Consultations\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConvertConsultationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('consultations.convert') === true;
    }

    public function rules(): array
    {
        return ['visa_service_id' => ['required', 'integer', 'exists:visa_services,id']];
    }
}
