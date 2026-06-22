<?php

namespace App\Modules\Blog\Requests;

use App\Modules\Blog\Models\BlogTag;
use Illuminate\Foundation\Http\FormRequest;

class StoreBlogTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', BlogTag::class) === true;
    }

    public function rules(): array
    {
        return ['is_active' => ['sometimes', 'boolean'], 'translations' => ['required', 'array', 'min:1'], 'translations.*.name' => ['required', 'string', 'max:150'], 'translations.*.slug' => ['required', 'string', 'max:180']];
    }
}
