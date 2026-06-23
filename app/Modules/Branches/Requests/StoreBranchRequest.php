<?php

namespace App\Modules\Branches\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('branches.create') ?? false;
    }

    public function rules(): array
    {
        return ['name' => ['required', 'string', 'max:255'], 'address' => ['required', 'string'], 'phone_numbers' => ['nullable', 'array'], 'whatsapp_number' => ['nullable', 'string', 'max:30'], 'email' => ['nullable', 'email'], 'google_maps_url' => ['nullable', 'url', 'max:1000'], 'working_hours' => ['nullable', 'string'], 'emirate' => ['nullable', 'string', 'max:100'], 'city' => ['nullable', 'string', 'max:100'], 'is_active' => ['sometimes', 'boolean'], 'sort_order' => ['sometimes', 'integer']];
    }
}
