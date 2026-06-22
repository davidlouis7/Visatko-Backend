<?php

namespace App\Modules\Consultations\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreConsultationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:30'],
            'whatsapp_number' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email:rfc', 'max:254'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'current_emirate' => ['nullable', 'string', 'max:100'],
            'preferred_destination_country_id' => ['nullable', 'integer', 'exists:countries,id'],
            'preferred_visa_service_id' => ['nullable', 'integer', 'exists:visa_services,id'],
            'are_you_residing_in_uae' => ['required', 'boolean'],
            'monthly_salary_range' => ['required', Rule::in(['below_5000', 'above_5000', 'above_10000', 'not_applicable'])],
            'salary_transferred_regularly' => ['nullable', 'boolean'],
            'has_tenancy_contract' => ['nullable', 'boolean'],
            'owns_car' => ['nullable', 'boolean'],
            'has_previous_travel_history' => ['nullable', 'boolean'],
            'previous_visa_refusal' => ['nullable', 'boolean'],
            'expected_travel_date' => ['nullable', 'date', 'after_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'source' => ['nullable', 'string', 'max:100'],
            'utm_source' => ['nullable', 'string', 'max:255'], 'utm_medium' => ['nullable', 'string', 'max:255'], 'utm_campaign' => ['nullable', 'string', 'max:255'], 'utm_content' => ['nullable', 'string', 'max:255'], 'utm_term' => ['nullable', 'string', 'max:255'], 'meta_event_id' => ['nullable', 'string', 'max:255'],
            'preferred_language' => ['nullable', 'string', 'exists:languages,code'],
        ];
    }
}
