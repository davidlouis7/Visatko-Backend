<?php

namespace App\Modules\Pages\Requests;

use App\Modules\Pages\Models\Page;
use Illuminate\Foundation\Http\FormRequest;

class StorePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Page::class) === true;
    }

    public function rules(): array
    {
        return ['key' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9_.-]+$/', 'unique:pages,key'], 'is_active' => ['sometimes', 'boolean'], 'translations' => ['required', 'array', 'min:1'], 'translations.*.title' => ['required', 'string', 'max:200'], 'translations.*.slug' => ['required', 'string', 'max:220'], 'translations.*.content' => ['required', 'string'], 'translations.*.meta_title' => ['nullable', 'string', 'max:255'], 'translations.*.meta_description' => ['nullable', 'string'], 'translations.*.meta_keywords' => ['nullable', 'string']];
    }
}
