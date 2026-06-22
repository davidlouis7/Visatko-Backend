<?php

namespace Tests\Feature\Customers;

use App\Models\User;
use App\Modules\Customers\Models\Customer;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CustomerManagementTest extends TestCase
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

    public function test_admin_can_create_search_update_show_and_deactivate_customer(): void
    {
        $response = $this->postJson('/api/v1/admin/customers', ['full_name' => 'Jane Customer', 'email' => 'jane@example.com', 'phone' => '+971501111111', 'preferred_language' => 'en'])->assertCreated();
        $id = $response->json('data.id');
        $this->getJson('/api/v1/admin/customers?search=501111111')->assertOk()->assertJsonCount(1, 'data');
        $this->patchJson("/api/v1/admin/customers/{$id}", ['emirate' => 'Dubai'])->assertOk()->assertJsonPath('data.emirate', 'Dubai');
        $this->getJson("/api/v1/admin/customers/{$id}")->assertOk()->assertJsonPath('data.email', 'jane@example.com');
        $this->deleteJson("/api/v1/admin/customers/{$id}")->assertOk();
        $this->assertSoftDeleted(Customer::class, ['id' => $id, 'is_active' => false]);
    }
}
