<?php

namespace App\Modules\Blog\Requests;

use App\Modules\Blog\Models\BlogCategory;
use Illuminate\Foundation\Http\FormRequest;

class StoreBlogCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', BlogCategory::class) === true;
    }

    public function rules(): array
    {
        return ['is_active' => ['sometimes', 'boolean'], 'sort_order' => ['sometimes', 'integer', 'min:0'], 'translations' => ['required', 'array', 'min:1'], 'translations.*.name' => ['required', 'string', 'max:150'], 'translations.*.slug' => ['required', 'string', 'max:180'], 'translations.*.description' => ['nullable', 'string'], 'translations.*.meta_title' => ['nullable', 'string', 'max:255'], 'translations.*.meta_description' => ['nullable', 'string'], 'translations.*.meta_keywords' => ['nullable', 'string']];
    }
}
