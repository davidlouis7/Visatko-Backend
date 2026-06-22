<?php

namespace App\Modules\Customers\Requests;

use App\Modules\Customers\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Customer::class) === true;
    }

    public function rules(): array
    {
        return ['full_name' => ['required', 'string', 'max:150'], 'email' => ['nullable', 'email:rfc', 'max:254', 'unique:customers,email'], 'phone' => ['required', 'string', 'max:30'], 'whatsapp_number' => ['nullable', 'string', 'max:30'], 'nationality' => ['nullable', 'string', 'max:100'], 'residence_country' => ['nullable', 'string', 'max:100'], 'emirate' => ['nullable', 'string', 'max:100'], 'preferred_language' => ['sometimes', 'string', 'exists:languages,code'], 'source' => ['nullable', 'string', 'max:100'], 'notes' => ['nullable', 'string', 'max:5000'], 'is_active' => ['sometimes', 'boolean']];
    }
}
