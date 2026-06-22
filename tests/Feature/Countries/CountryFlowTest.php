<?php

namespace Tests\Feature\Countries;

use App\Models\User;
use App\Modules\Countries\Models\Country;
use App\Modules\Languages\Models\Language;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CountryFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RolePermissionSeeder::class, LanguageSeeder::class]);
        $user = User::factory()->create();
        $user->assignRole('Super Admin');
        Sanctum::actingAs($user);
    }

    public function test_admin_creates_translated_country_and_public_api_resolves_query_locale(): void
    {
        $this->postJson('/api/v1/admin/countries', ['code' => 'GB', 'is_active' => true, 'translations' => ['en' => ['name' => 'United Kingdom', 'slug' => 'united-kingdom'], 'ar' => ['name' => 'المملكة المتحدة', 'slug' => 'المملكة-المتحدة']]])
            ->assertCreated()->assertJsonPath('data.translations.en.name', 'United Kingdom');

        $this->getJson('/api/v1/countries/'.rawurlencode('المملكة-المتحدة').'?locale=ar')
            ->assertOk()->assertJsonPath('data.name', 'المملكة المتحدة')->assertJsonPath('data.slug', 'المملكة-المتحدة');
        $this->assertDatabaseHas('country_translations', ['slug' => 'united-kingdom']);
    }

    public function test_header_locale_and_active_filter_are_applied_to_public_list(): void
    {
        $active = Country::factory()->create(['code' => 'FR']);
        $inactive = Country::factory()->create(['code' => 'DE', 'is_active' => false]);
        $ar = Language::query()->where('code', 'ar')->firstOrFail();
        $active->translations()->create(['language_id' => $ar->id, 'name' => 'فرنسا', 'slug' => 'فرنسا']);
        $inactive->translations()->create(['language_id' => $ar->id, 'name' => 'ألمانيا', 'slug' => 'ألمانيا']);

        $this->withHeader('Accept-Language', 'ar-AE,ar;q=0.9')->getJson('/api/v1/countries')
            ->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.name', 'فرنسا');
    }
}
