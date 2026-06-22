<?php

namespace App\Modules\VisaServices\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVisaServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('visa_service')) === true;
    }

    public function rules(): array
    {
        return ['country_id' => ['sometimes', 'required', 'integer', 'exists:countries,id'], 'thumbnail_media_id' => ['sometimes', 'nullable', 'integer', 'exists:media,id'], 'banner_media_id' => ['sometimes', 'nullable', 'integer', 'exists:media,id'], 'price' => ['sometimes', 'required', 'numeric', 'min:0'], 'discount_price' => ['sometimes', 'nullable', 'numeric', 'min:0'], 'currency' => ['sometimes', 'required', 'string', 'size:3', 'uppercase'], 'processing_time' => ['sometimes', 'nullable', 'string', 'max:255'], 'visa_validity' => ['sometimes', 'nullable', 'string', 'max:255'], 'stay_duration' => ['sometimes', 'nullable', 'string', 'max:255'], 'is_featured' => ['sometimes', 'boolean'], 'is_active' => ['sometimes', 'boolean'], 'sort_order' => ['sometimes', 'integer', 'min:0'], 'gallery_media_ids' => ['sometimes', 'array'], 'gallery_media_ids.*' => ['integer', 'distinct', 'exists:media,id'], 'translations' => ['sometimes', 'required', 'array', 'min:1'], 'translations.*.title' => ['required', 'string', 'max:180'], 'translations.*.slug' => ['required', 'string', 'max:200'], 'translations.*.short_description' => ['nullable', 'string'], 'translations.*.full_description' => ['nullable', 'string'], 'translations.*.requirements' => ['nullable', 'string'], 'translations.*.required_documents' => ['nullable', 'string'], 'translations.*.terms_conditions' => ['nullable', 'string'], 'translations.*.meta_title' => ['nullable', 'string', 'max:255'], 'translations.*.meta_description' => ['nullable', 'string'], 'translations.*.meta_keywords' => ['nullable', 'string']];
    }
}
