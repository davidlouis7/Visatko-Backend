<?php

namespace App\Modules\Consultations\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignConsultationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('consultations.assign') === true;
    }

    public function rules(): array
    {
        return ['assigned_to' => ['required', 'integer', 'exists:users,id']];
    }
}
