<?php

namespace Database\Seeders;

use App\Modules\Settings\Models\Setting;
use Illuminate\Database\Seeder;

class MarketingSettingSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['meta_pixel_enabled', false, 'boolean', true],
            ['meta_pixel_id', null, 'string', true],
            ['meta_capi_enabled', false, 'boolean', false],
            ['meta_capi_token', null, 'string', false],
            ['meta_capi_test_event_code', null, 'string', false],
            ['google_analytics_enabled', false, 'boolean', true],
            ['google_analytics_id', null, 'string', true],
            ['google_tag_manager_enabled', false, 'boolean', true],
            ['google_tag_manager_id', null, 'string', true],
            ['tracking_debug_enabled', false, 'boolean', false],
        ] as [$key, $value, $type, $public]) {
            $setting = Setting::query()->firstOrNew(['group' => 'marketing', 'key' => $key]);
            $setting->fill(['type' => $type, 'is_public' => $public, 'is_encrypted' => false]);
            $setting->value = $setting->encodeValue($value);
            $setting->save();
        }
    }
}
