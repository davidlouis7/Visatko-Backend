<?php

namespace App\Modules\VisaApplications\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVisaApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['visa_service_id' => ['required', 'integer', 'exists:visa_services,id'], 'full_name' => ['required', 'string', 'max:150'], 'email' => ['nullable', 'email:rfc', 'max:254'], 'phone' => ['required', 'string', 'max:30'], 'whatsapp_number' => ['nullable', 'string', 'max:30'], 'nationality' => ['nullable', 'string', 'max:100'], 'residence_country' => ['nullable', 'string', 'max:100'], 'emirate' => ['nullable', 'string', 'max:100'], 'passport_number' => ['nullable', 'string', 'max:100'], 'travel_date' => ['nullable', 'date', 'after_or_equal:today'], 'customer_notes' => ['nullable', 'string', 'max:5000'], 'source' => ['nullable', 'string', 'max:100'], 'preferred_language' => ['nullable', 'string', 'exists:languages,code']];
    }
}
