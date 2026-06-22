<?php

namespace Database\Seeders;

use App\Modules\Countries\Models\Country;
use App\Modules\Languages\Models\Language;
use App\Modules\VisaServices\Models\VisaService;
use Illuminate\Database\Seeder;

class VisaServiceSeeder extends Seeder
{
    public function run(): void
    {
        $country = Country::query()->where('code', 'GB')->firstOrFail();
        $service = VisaService::query()->firstOrCreate(['country_id' => $country->id, 'processing_time' => '15 working days'], ['price' => 1200, 'currency' => 'AED', 'is_featured' => true, 'is_active' => true]);
        $language = Language::query()->where('code', 'en')->firstOrFail();
        $service->translations()->updateOrCreate(['language_id' => $language->id], ['title' => 'UK Tourist Visa', 'slug' => 'uk-tourist-visa', 'short_description' => 'Tourist visa assistance for the United Kingdom.']);
    }
}
