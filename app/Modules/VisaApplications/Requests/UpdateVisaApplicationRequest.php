<?php

namespace App\Modules\VisaApplications\Requests;

use App\Modules\VisaApplications\Enums\PaymentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVisaApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('visa_application')) === true;
    }

    public function rules(): array
    {
        return ['full_name' => ['sometimes', 'required', 'string', 'max:150'], 'email' => ['sometimes', 'nullable', 'email:rfc', 'max:254'], 'phone' => ['sometimes', 'required', 'string', 'max:30'], 'whatsapp_number' => ['sometimes', 'nullable', 'string', 'max:30'], 'nationality' => ['sometimes', 'nullable', 'string', 'max:100'], 'residence_country' => ['sometimes', 'nullable', 'string', 'max:100'], 'emirate' => ['sometimes', 'nullable', 'string', 'max:100'], 'passport_number' => ['sometimes', 'nullable', 'string', 'max:100'], 'travel_date' => ['sometimes', 'nullable', 'date'], 'payment_status' => ['sometimes', Rule::enum(PaymentStatus::class)], 'customer_notes' => ['sometimes', 'nullable', 'string', 'max:5000'], 'internal_notes' => ['sometimes', 'nullable', 'string', 'max:10000']];
    }
}
