<?php

namespace Tests\Feature\Finance;

use App\Modules\Finance\Services\FinanceSettings;
use App\Modules\Settings\Models\Setting;
use Database\Seeders\FinanceSettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceSettingSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_finance_settings_seed_safely(): void
    {
        $this->seed(FinanceSettingSeeder::class);

        $this->assertDatabaseHas('settings', ['group' => 'finance', 'key' => 'vat_enabled']);
        $this->assertTrue(app(FinanceSettings::class)->bool('vat_enabled'));
        $this->assertSame('Visatko Visa Service', Setting::query()->where('group', 'finance')->where('key', 'company_legal_name')->first()->resolvedValue());
    }
}
