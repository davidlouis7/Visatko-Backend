<?php

namespace App\Modules\Languages\Requests;

use App\Modules\Languages\Models\Language;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLanguageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('language')) === true;
    }

    public function rules(): array
    {
        /** @var Language $language */
        $language = $this->route('language');

        return [
            'code' => ['sometimes', 'required', 'string', 'max:10', 'regex:/^[a-z]{2,3}(-[A-Z]{2})?$/', Rule::unique('languages', 'code')->ignore($language)],
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'native_name' => ['sometimes', 'required', 'string', 'max:100'],
            'direction' => ['sometimes', 'required', Rule::in(['ltr', 'rtl'])],
            'fallback_code' => ['sometimes', 'nullable', 'string', 'max:10', Rule::exists('languages', 'code')],
            'is_active' => ['sometimes', 'boolean'],
            'is_default' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}
