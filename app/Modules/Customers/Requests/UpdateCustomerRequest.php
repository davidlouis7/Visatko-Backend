<?php

namespace App\Modules\Customers\Requests;

use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends StoreCustomerRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('customer')) === true;
    }

    public function rules(): array
    {
        $rules = array_map(fn (array $rules): array => array_merge(['sometimes'], $rules), parent::rules());
        $rules['email'] = ['sometimes', 'nullable', 'email:rfc', 'max:254', Rule::unique('customers', 'email')->ignore($this->route('customer'))];

        return $rules;
    }
}
