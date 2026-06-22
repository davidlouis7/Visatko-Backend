<?php

namespace Tests\Feature\Languages;

use App\Models\User;
use App\Modules\Languages\Models\Language;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LanguageManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RolePermissionSeeder::class, LanguageSeeder::class]);
        $admin = User::factory()->create();
        $admin->assignRole('Super Admin');
        Sanctum::actingAs($admin);
    }

    public function test_admin_can_create_and_update_a_language(): void
    {
        $response = $this->postJson('/api/v1/admin/languages', [
            'code' => 'fr', 'name' => 'French', 'native_name' => 'Français',
            'direction' => 'ltr', 'fallback_code' => 'en', 'is_active' => true,
        ])->assertCreated()->assertJsonPath('data.code', 'fr');

        $language = Language::query()->where('code', 'fr')->firstOrFail();
        $this->patchJson('/api/v1/admin/languages/'.$language->id, [
            'name' => 'French language', 'sort_order' => 3,
        ])->assertOk()->assertJsonPath('data.sort_order', 3);

        $this->assertDatabaseHas('activity_log', ['description' => 'Language updated']);
    }

    public function test_public_endpoint_returns_only_active_languages(): void
    {
        Language::query()->where('code', 'ar')->update(['is_active' => false]);

        $this->getJson('/api/v1/languages')->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.code', 'en');
    }
}
