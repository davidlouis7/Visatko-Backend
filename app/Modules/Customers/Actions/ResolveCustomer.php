<?php

namespace App\Modules\Customers\Actions;

use App\Modules\Customers\Models\Customer;

class ResolveCustomer
{
    public function execute(array $data): Customer
    {
        $customer = Customer::withTrashed()->where('phone', $data['phone'])->first();
        $customer ??= isset($data['email']) ? Customer::withTrashed()->where('email', $data['email'])->first() : null;

        if (! $customer) {
            return Customer::query()->create($this->attributes($data));
        }

        $customer->restore();
        $attributes = array_filter($this->attributes($data), fn ($value): bool => $value !== null && $value !== '');
        if (isset($attributes['email']) && Customer::withTrashed()->where('email', $attributes['email'])->whereKeyNot($customer->id)->exists()) {
            unset($attributes['email']);
        }
        $customer->fill($attributes)->save();

        return $customer;
    }

    private function attributes(array $data): array
    {
        return ['full_name' => $data['full_name'], 'email' => $data['email'] ?? null, 'phone' => $data['phone'], 'whatsapp_number' => $data['whatsapp_number'] ?? null, 'nationality' => $data['nationality'] ?? null, 'residence_country' => $data['residence_country'] ?? null, 'emirate' => $data['emirate'] ?? $data['current_emirate'] ?? null, 'preferred_language' => $data['preferred_language'] ?? app()->getLocale(), 'source' => $data['source'] ?? 'website', 'is_active' => true];
    }
}
