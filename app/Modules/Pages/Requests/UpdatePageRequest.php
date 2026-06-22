<?php

namespace App\Modules\Pages\Requests;

use Illuminate\Validation\Rule;

class UpdatePageRequest extends StorePageRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('page')) === true;
    }

    public function rules(): array
    {
        $rules = array_map(fn (array $rules): array => array_merge(['sometimes'], $rules), parent::rules());
        $rules['key'] = ['sometimes', 'required', 'string', 'max:100', 'regex:/^[a-z0-9_.-]+$/', Rule::unique('pages', 'key')->ignore($this->route('page'))];

        return $rules;
    }
}
