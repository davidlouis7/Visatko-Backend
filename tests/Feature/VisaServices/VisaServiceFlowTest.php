<?php

namespace Tests\Feature\VisaServices;

use App\Models\User;
use App\Modules\Countries\Models\Country;
use App\Modules\Languages\Models\Language;
use App\Modules\VisaServices\Models\VisaService;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class VisaServiceFlowTest extends TestCase
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

    public function test_admin_creates_service_and_public_featured_and_slug_endpoints_return_it(): void
    {
        $country = Country::factory()->create(['code' => 'GB']);
        $en = Language::query()->where('code', 'en')->firstOrFail();
        $country->translations()->create(['language_id' => $en->id, 'name' => 'United Kingdom', 'slug' => 'united-kingdom']);

        $response = $this->postJson('/api/v1/admin/visa-services', ['country_id' => $country->id, 'price' => 1250, 'discount_price' => 1100, 'currency' => 'AED', 'processing_time' => '15 days', 'is_featured' => true, 'is_active' => true, 'translations' => ['en' => ['title' => 'UK Tourist Visa', 'slug' => 'uk-tourist-visa', 'short_description' => 'Apply with Visatko', 'required_documents' => 'Passport, photo']]])
            ->assertCreated()->assertJsonPath('data.title', 'UK Tourist Visa');
        $id = $response->json('data.id');

        $this->getJson('/api/v1/visa-services/featured')->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $id);
        $this->getJson('/api/v1/visa-services/uk-tourist-visa')->assertOk()->assertJsonPath('data.discount_price', '1100.00');
    }

    public function test_inactive_service_is_hidden_publicly_but_available_to_admin(): void
    {
        $service = VisaService::factory()->create(['is_active' => false]);
        $en = Language::query()->where('code', 'en')->firstOrFail();
        $service->translations()->create(['language_id' => $en->id, 'title' => 'Hidden Visa', 'slug' => 'hidden-visa']);

        $this->getJson('/api/v1/visa-services')->assertOk()->assertJsonCount(0, 'data');
        $this->getJson('/api/v1/admin/visa-services')->assertOk()->assertJsonCount(1, 'data');
    }
}
