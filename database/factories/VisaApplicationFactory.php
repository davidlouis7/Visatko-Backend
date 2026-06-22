<?php

namespace Database\Factories;

use App\Modules\Customers\Models\Customer;
use App\Modules\VisaApplications\Models\VisaApplication;
use App\Modules\VisaServices\Models\VisaService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VisaApplicationFactory extends Factory
{
    protected $model = VisaApplication::class;

    public function definition(): array
    {
        return ['application_number' => 'VSA-'.now()->format('Ym').'-'.strtoupper(Str::random(8)), 'customer_id' => Customer::factory(), 'visa_service_id' => VisaService::factory(), 'full_name' => fake()->name(), 'email' => fake()->safeEmail(), 'phone' => fake()->e164PhoneNumber(), 'status' => 'new', 'payment_status' => 'unpaid', 'submitted_at' => now()];
    }
}
