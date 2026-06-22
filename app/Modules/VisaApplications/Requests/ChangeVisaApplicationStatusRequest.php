<?php

namespace App\Modules\VisaApplications\Requests;

use App\Modules\VisaApplications\Enums\ApplicationStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangeVisaApplicationStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('applications.change_status') === true;
    }

    public function rules(): array
    {
        return ['status' => ['required', Rule::enum(ApplicationStatus::class)], 'description' => ['nullable', 'string', 'max:2000']];
    }
}
