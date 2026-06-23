<?php

namespace Database\Seeders;

use App\Modules\Branches\Models\Branch;
use App\Modules\Counters\Models\Counter;
use Illuminate\Database\Seeder;

class BusinessContentSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['happy_customers', 'Happy Customers', 1000, '+', 1],
            ['approved_visas', 'Approved Visas', 500, '+', 2],
            ['years_experience', 'Years Experience', 5, '+', 3],
            ['partner_companies', 'Partner Companies', 25, '+', 4],
        ] as [$key, $label, $value, $suffix, $sort]) {
            Counter::query()->updateOrCreate(['key' => $key], ['label' => $label, 'value' => $value, 'suffix' => $suffix, 'is_active' => true, 'sort_order' => $sort]);
        }

        Branch::query()->updateOrCreate(['name' => 'Visatko Visa Service - Ajman'], [
            'address' => 'Ajman, United Arab Emirates',
            'phone_numbers' => [],
            'email' => null,
            'emirate' => 'Ajman',
            'city' => 'Ajman',
            'is_active' => true,
            'sort_order' => 1,
        ]);
    }
}
