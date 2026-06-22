<?php

namespace App\Modules\Countries\Requests;

use App\Modules\Countries\Models\Country;
use Illuminate\Foundation\Http\FormRequest;

class StoreCountryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Country::class) === true;
    }

    public function rules(): array
    {
        return ['code' => ['required', 'string', 'size:2', 'alpha', 'unique:countries,code'], 'flag_media_id' => ['nullable', 'integer', 'exists:media,id'], 'is_active' => ['sometimes', 'boolean'], 'sort_order' => ['sometimes', 'integer', 'min:0'], 'translations' => ['required', 'array', 'min:1'], 'translations.*.name' => ['required', 'string', 'max:150'], 'translations.*.slug' => ['required', 'string', 'max:180', 'regex:/^[a-z0-9\pL]+(?:-[a-z0-9\pL]+)*$/u'], 'translations.*.description' => ['nullable', 'string'], 'translations.*.meta_title' => ['nullable', 'string', 'max:255'], 'translations.*.meta_description' => ['nullable', 'string'], 'translations.*.meta_keywords' => ['nullable', 'string']];
    }
}
