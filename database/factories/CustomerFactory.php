<?php

namespace Database\Factories;

use App\Modules\Customers\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return ['full_name' => fake()->name(), 'email' => fake()->unique()->safeEmail(), 'phone' => fake()->unique()->e164PhoneNumber(), 'whatsapp_number' => fake()->e164PhoneNumber(), 'nationality' => fake()->country(), 'residence_country' => 'United Arab Emirates', 'emirate' => 'Dubai', 'preferred_language' => 'en', 'source' => 'website', 'is_active' => true];
    }
}
