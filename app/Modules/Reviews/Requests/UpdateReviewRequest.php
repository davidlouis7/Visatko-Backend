<?php

namespace App\Modules\Reviews\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('reviews.update') ?? false;
    }

    public function rules(): array
    {
        return ['customer_name' => ['sometimes', 'string', 'max:255'], 'customer_country' => ['nullable', 'string', 'max:100'], 'visa_service_id' => ['nullable', 'exists:visa_services,id'], 'rating' => ['sometimes', 'integer', 'between:1,5'], 'review_text' => ['sometimes', 'string'], 'customer_image_media_id' => ['nullable', 'exists:media,id'], 'is_active' => ['sometimes', 'boolean'], 'is_featured' => ['sometimes', 'boolean'], 'sort_order' => ['sometimes', 'integer', 'min:0']];
    }
}
