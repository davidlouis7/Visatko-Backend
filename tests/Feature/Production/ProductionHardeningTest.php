<?php

namespace Tests\Feature\Production;

use App\Models\User;
use App\Modules\Counters\Models\Counter;
use Database\Seeders\BusinessContentSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductionHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_health_endpoint_returns_system_checks_and_secure_headers(): void
    {
        $response = $this->getJson('/api/v1/system/health')->assertOk();

        $response->assertJsonStructure(['success', 'data' => ['status', 'checks' => ['app', 'database', 'redis', 'queue', 'cache', 'storage']]]);
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_login_route_is_rate_limited(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/v1/auth/login', ['email' => 'nobody@example.com', 'password' => 'bad'])->assertStatus(422);
        }

        $this->postJson('/api/v1/auth/login', ['email' => 'nobody@example.com', 'password' => 'bad'])->assertTooManyRequests();
    }

    public function test_api_forces_json_for_validation_errors(): void
    {
        $this->post('/api/v1/public/contact-messages', [])
            ->assertStatus(422)
            ->assertHeader('content-type', 'application/json')
            ->assertJsonPath('success', false);
    }

    public function test_public_response_cache_is_invalidated_when_content_changes(): void
    {
        $this->seed([RolePermissionSeeder::class, BusinessContentSeeder::class]);
        $admin = User::factory()->create();
        $admin->assignRole('Super Admin');
        Sanctum::actingAs($admin);

        $this->getJson('/api/v1/public/counters')->assertOk()->assertJsonFragment(['label' => 'Happy Customers']);
        $counter = Counter::query()->where('key', 'happy_customers')->firstOrFail();
        $this->patchJson("/api/v1/admin/counters/{$counter->id}", ['label' => 'Happy Clients'])->assertOk();
        $this->getJson('/api/v1/public/counters')->assertOk()->assertJsonFragment(['label' => 'Happy Clients']);
    }

    public function test_openapi_documentation_route_is_available(): void
    {
        $this->get('/api/documentation')->assertOk()->assertSee('SwaggerUIBundle');
        $this->get('/openapi.yaml')->assertOk()->assertSee('Visatko Backend API');
    }
}
