<?php

namespace Database\Seeders;

use App\Modules\Languages\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    public function run(): void
    {
        Language::query()->updateOrCreate(['code' => 'en'], [
            'name' => 'English', 'native_name' => 'English', 'direction' => 'ltr',
            'is_active' => true, 'is_default' => true, 'sort_order' => 1,
        ]);

        Language::query()->updateOrCreate(['code' => 'ar'], [
            'name' => 'Arabic', 'native_name' => 'العربية', 'direction' => 'rtl',
            'fallback_code' => 'en', 'is_active' => true, 'is_default' => false, 'sort_order' => 2,
        ]);
    }
}
