<?php

namespace App\Modules\Counters\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCounterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('counters.create') ?? false;
    }

    public function rules(): array
    {
        return ['key' => ['required', 'string', 'max:100', 'unique:counters,key'], 'label' => ['required', 'string', 'max:255'], 'value' => ['required', 'integer'], 'suffix' => ['nullable', 'string', 'max:20'], 'is_active' => ['sometimes', 'boolean'], 'sort_order' => ['sometimes', 'integer']];
    }
}
