<?php

namespace Database\Factories;

use App\Modules\Countries\Models\Country;
use App\Modules\VisaServices\Models\VisaService;
use Illuminate\Database\Eloquent\Factories\Factory;

class VisaServiceFactory extends Factory
{
    protected $model = VisaService::class;

    public function definition(): array
    {
        return ['country_id' => Country::factory(), 'price' => fake()->randomFloat(2, 100, 5000), 'currency' => 'AED', 'processing_time' => '5-10 days', 'visa_validity' => '90 days', 'stay_duration' => '30 days', 'is_featured' => false, 'is_active' => true, 'sort_order' => 0];
    }
}
