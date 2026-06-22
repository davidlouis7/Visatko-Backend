<?php

namespace Tests\Feature\Pages;

use App\Models\User;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PageFlowTest extends TestCase
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

    public function test_admin_creates_multilingual_page_and_public_slug_is_localized(): void
    {
        $response = $this->postJson('/api/v1/admin/pages', ['key' => 'about', 'is_active' => true, 'translations' => ['en' => ['title' => 'About Us', 'slug' => 'about-us', 'content' => 'About Visatko'], 'ar' => ['title' => 'من نحن', 'slug' => 'من-نحن', 'content' => 'عن فيزاتكو']]])
            ->assertCreated()->assertJsonPath('data.translations.ar.title', 'من نحن');

        $this->getJson('/api/v1/pages/'.rawurlencode('من-نحن').'?locale=ar')->assertOk()->assertJsonPath('data.content', 'عن فيزاتكو');
        $this->getJson('/api/v1/pages')->assertOk()->assertJsonCount(1, 'data');
        $this->deleteJson('/api/v1/admin/pages/'.$response->json('data.id'))->assertOk();
        $this->getJson('/api/v1/pages')->assertOk()->assertJsonCount(0, 'data');
    }
}
