<?php

namespace Database\Seeders;

use App\Modules\Settings\Models\Setting;
use Illuminate\Database\Seeder;

class EmailSettingSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['email_enabled', true, 'boolean', false],
            ['admin_notification_email', null, 'string', false],
            ['from_name', 'Visatko Visa Service', 'string', false],
            ['from_email', null, 'string', false],
            ['frontend_url', env('FRONTEND_URL', env('APP_URL')), 'string', true],
            ['invoice_public_base_url', rtrim((string) env('FRONTEND_URL', env('APP_URL')), '/').'/invoices', 'string', false],
            ['application_public_base_url', rtrim((string) env('FRONTEND_URL', env('APP_URL')), '/').'/applications', 'string', false],
        ] as [$key, $value, $type, $public]) {
            $setting = Setting::query()->firstOrNew(['group' => 'email', 'key' => $key]);
            $setting->fill(['type' => $type, 'is_public' => $public, 'is_encrypted' => false]);
            $setting->value = $setting->encodeValue($value);
            $setting->save();
        }
    }
}
