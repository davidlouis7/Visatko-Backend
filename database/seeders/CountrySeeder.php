<?php

namespace Database\Seeders;

use App\Modules\Countries\Models\Country;
use App\Modules\Languages\Models\Language;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $languages = Language::query()->whereIn('code', ['en', 'ar'])->get()->keyBy('code');
        foreach ([
            ['code' => 'AE', 'en' => ['name' => 'United Arab Emirates', 'slug' => 'united-arab-emirates'], 'ar' => ['name' => 'الإمارات العربية المتحدة', 'slug' => 'الإمارات-العربية-المتحدة']],
            ['code' => 'GB', 'en' => ['name' => 'United Kingdom', 'slug' => 'united-kingdom'], 'ar' => ['name' => 'المملكة المتحدة', 'slug' => 'المملكة-المتحدة']],
        ] as $row) {
            $country = Country::query()->updateOrCreate(['code' => $row['code']], ['is_active' => true]);
            foreach (['en', 'ar'] as $locale) {
                if (isset($languages[$locale])) {
                    $country->translations()->updateOrCreate(['language_id' => $languages[$locale]->id], $row[$locale]);
                }
            }
        }
    }
}
