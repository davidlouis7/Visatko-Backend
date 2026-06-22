<?php

namespace App\Modules\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:150'],
            'email' => ['sometimes', 'required', 'email:rfc', 'max:254', Rule::unique('users')->ignore($this->user())],
            'phone' => ['sometimes', 'nullable', 'string', 'max:30'],
        ];
    }
}
