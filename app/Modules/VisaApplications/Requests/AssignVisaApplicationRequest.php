<?php

namespace App\Modules\VisaApplications\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignVisaApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('applications.assign') === true;
    }

    public function rules(): array
    {
        return ['assigned_to' => ['required', 'integer', 'exists:users,id']];
    }
}
