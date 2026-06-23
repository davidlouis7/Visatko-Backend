<?php

namespace App\Modules\Counters\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCounterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('counters.update') ?? false;
    }

    public function rules(): array
    {
        return ['key' => ['sometimes', 'string', 'max:100', Rule::unique('counters', 'key')->ignore($this->route('counter'))], 'label' => ['sometimes', 'string', 'max:255'], 'value' => ['sometimes', 'integer'], 'suffix' => ['nullable', 'string', 'max:20'], 'is_active' => ['sometimes', 'boolean'], 'sort_order' => ['sometimes', 'integer']];
    }
}
