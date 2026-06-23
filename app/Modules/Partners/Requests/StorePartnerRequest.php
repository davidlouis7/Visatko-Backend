<?php

namespace App\Modules\Partners\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePartnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('partners.create') ?? false;
    }

    public function rules(): array
    {
        return ['company_name' => ['required', 'string', 'max:255'], 'logo_media_id' => ['nullable', 'exists:media,id'], 'website_url' => ['nullable', 'url', 'max:1000'], 'description' => ['nullable', 'string'], 'is_active' => ['sometimes', 'boolean'], 'sort_order' => ['sometimes', 'integer']];
    }
}
