<?php

namespace Tests\Feature\Authorization;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolesAndPermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_required_roles_and_permissions_are_seeded(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $this->assertEqualsCanonicalizing(
            ['Super Admin', 'Admin', 'Accountant', 'Visa Consultant', 'CRM User'],
            Role::query()->pluck('name')->all(),
        );
        $this->assertTrue(Role::findByName('Accountant')->hasPermissionTo('payments.review'));
        $this->assertFalse(Role::findByName('CRM User')->hasPermissionTo('settings.update'));
    }

    public function test_permission_policy_blocks_and_then_allows_language_management(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/admin/languages')->assertForbidden();

        $user->givePermissionTo('languages.view');
        $this->getJson('/api/v1/admin/languages')->assertOk();
    }
}
