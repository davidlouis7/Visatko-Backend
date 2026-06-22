<?php

namespace Tests\Feature\Settings;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SettingManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole('Super Admin');
        Sanctum::actingAs($admin);
    }

    public function test_admin_can_create_typed_public_setting(): void
    {
        $this->postJson('/api/v1/admin/settings', [
            'group' => 'general', 'key' => 'maintenance_mode',
            'value' => false, 'type' => 'boolean', 'is_public' => true,
        ])->assertCreated()->assertJsonPath('data.value', false);

        $this->getJson('/api/v1/settings/public')
            ->assertOk()->assertJsonPath('data.general.maintenance_mode', false);
    }

    public function test_encrypted_setting_is_stored_encrypted_and_never_exposed_publicly(): void
    {
        $this->postJson('/api/v1/admin/settings', [
            'group' => 'tracking', 'key' => 'meta_capi_token',
            'value' => 'top-secret', 'type' => 'string', 'is_encrypted' => true,
        ])->assertCreated()->assertJsonPath('data.value', null);

        $this->assertDatabaseMissing('settings', ['value' => 'top-secret']);
        $this->getJson('/api/v1/settings/public')->assertJsonMissing(['meta_capi_token']);
    }
}
