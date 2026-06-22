<?php

namespace Database\Factories;

use App\Modules\Countries\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

class CountryFactory extends Factory
{
    protected $model = Country::class;

    public function definition(): array
    {
        return ['code' => fake()->unique()->countryCode(), 'is_active' => true, 'sort_order' => 0];
    }
}
