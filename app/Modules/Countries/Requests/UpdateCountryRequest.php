<?php

namespace App\Modules\Countries\Requests;

use App\Modules\Countries\Models\Country;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCountryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('country')) === true;
    }

    public function rules(): array
    {
        /** @var Country $country */ $country = $this->route('country');

        return ['code' => ['sometimes', 'required', 'string', 'size:2', 'alpha', Rule::unique('countries', 'code')->ignore($country)], 'flag_media_id' => ['sometimes', 'nullable', 'integer', 'exists:media,id'], 'is_active' => ['sometimes', 'boolean'], 'sort_order' => ['sometimes', 'integer', 'min:0'], 'translations' => ['sometimes', 'required', 'array', 'min:1'], 'translations.*.name' => ['required', 'string', 'max:150'], 'translations.*.slug' => ['required', 'string', 'max:180'], 'translations.*.description' => ['nullable', 'string'], 'translations.*.meta_title' => ['nullable', 'string', 'max:255'], 'translations.*.meta_description' => ['nullable', 'string'], 'translations.*.meta_keywords' => ['nullable', 'string']];
    }
}
