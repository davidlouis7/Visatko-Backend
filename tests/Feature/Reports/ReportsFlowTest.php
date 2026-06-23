<?php

namespace Tests\Feature\Reports;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ReportsFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_reports_return_summary_and_are_permission_protected(): void
    {
        $this->seed(RolePermissionSeeder::class);
        Sanctum::actingAs(User::factory()->create());
        $this->getJson('/api/v1/admin/reports/dashboard')->assertForbidden();

        $admin = User::factory()->create();
        $admin->assignRole('Super Admin');
        Sanctum::actingAs($admin);
        $this->getJson('/api/v1/admin/reports/dashboard')->assertOk()->assertJsonStructure(['data' => ['total_customers', 'total_revenue', 'applications_by_status']]);
        $this->getJson('/api/v1/admin/reports/sales')->assertOk()->assertJsonStructure(['data' => ['revenue_by_day', 'net_revenue']]);
        $this->getJson('/api/v1/admin/reports/employee-performance')->assertOk()->assertJsonPath('success', true);
    }
}
