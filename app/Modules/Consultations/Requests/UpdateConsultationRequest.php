<?php

namespace App\Modules\Consultations\Requests;

use App\Modules\Consultations\Enums\ConsultationStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateConsultationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('consultation')) === true;
    }

    public function rules(): array
    {
        return ['status' => ['sometimes', Rule::enum(ConsultationStatus::class)], 'notes' => ['sometimes', 'nullable', 'string', 'max:5000'], 'expected_travel_date' => ['sometimes', 'nullable', 'date']];
    }
}
