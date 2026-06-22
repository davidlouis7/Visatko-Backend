<?php

namespace Database\Factories;

use App\Modules\Consultations\Models\Consultation;
use App\Modules\Customers\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConsultationFactory extends Factory
{
    protected $model = Consultation::class;

    public function definition(): array
    {
        return ['customer_id' => Customer::factory(), 'full_name' => fake()->name(), 'phone' => fake()->e164PhoneNumber(), 'whatsapp_number' => fake()->e164PhoneNumber(), 'email' => fake()->safeEmail(), 'nationality' => fake()->country(), 'current_emirate' => 'Dubai', 'are_you_residing_in_uae' => true, 'monthly_salary_range' => 'above_5000', 'status' => 'new', 'source' => 'website'];
    }
}
