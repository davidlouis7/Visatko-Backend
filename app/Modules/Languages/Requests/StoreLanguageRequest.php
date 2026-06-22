<?php

namespace App\Modules\Languages\Requests;

use App\Modules\Languages\Models\Language;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLanguageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Language::class) === true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:10', 'regex:/^[a-z]{2,3}(-[A-Z]{2})?$/', 'unique:languages,code'],
            'name' => ['required', 'string', 'max:100'],
            'native_name' => ['required', 'string', 'max:100'],
            'direction' => ['required', Rule::in(['ltr', 'rtl'])],
            'fallback_code' => ['nullable', 'string', 'max:10', Rule::exists('languages', 'code')],
            'is_active' => ['sometimes', 'boolean'],
            'is_default' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}
