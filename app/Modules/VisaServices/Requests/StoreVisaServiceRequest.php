<?php

namespace App\Modules\VisaServices\Requests;

use App\Modules\VisaServices\Models\VisaService;
use Illuminate\Foundation\Http\FormRequest;

class StoreVisaServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', VisaService::class) === true;
    }

    public function rules(): array
    {
        return ['country_id' => ['required', 'integer', 'exists:countries,id'], 'thumbnail_media_id' => ['nullable', 'integer', 'exists:media,id'], 'banner_media_id' => ['nullable', 'integer', 'exists:media,id'], 'price' => ['required', 'numeric', 'min:0'], 'discount_price' => ['nullable', 'numeric', 'min:0', 'lte:price'], 'currency' => ['required', 'string', 'size:3', 'uppercase'], 'processing_time' => ['nullable', 'string', 'max:255'], 'visa_validity' => ['nullable', 'string', 'max:255'], 'stay_duration' => ['nullable', 'string', 'max:255'], 'is_featured' => ['sometimes', 'boolean'], 'is_active' => ['sometimes', 'boolean'], 'sort_order' => ['sometimes', 'integer', 'min:0'], 'gallery_media_ids' => ['sometimes', 'array'], 'gallery_media_ids.*' => ['integer', 'distinct', 'exists:media,id'], 'translations' => ['required', 'array', 'min:1'], 'translations.*.title' => ['required', 'string', 'max:180'], 'translations.*.slug' => ['required', 'string', 'max:200'], 'translations.*.short_description' => ['nullable', 'string'], 'translations.*.full_description' => ['nullable', 'string'], 'translations.*.requirements' => ['nullable', 'string'], 'translations.*.required_documents' => ['nullable', 'string'], 'translations.*.terms_conditions' => ['nullable', 'string'], 'translations.*.meta_title' => ['nullable', 'string', 'max:255'], 'translations.*.meta_description' => ['nullable', 'string'], 'translations.*.meta_keywords' => ['nullable', 'string']];
    }
}
