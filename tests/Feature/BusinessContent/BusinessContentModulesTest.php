<?php

namespace Tests\Feature\BusinessContent;

use App\Models\User;
use Database\Seeders\BusinessContentSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BusinessContentModulesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RolePermissionSeeder::class, BusinessContentSeeder::class]);
        $admin = User::factory()->create();
        $admin->assignRole('Super Admin');
        Sanctum::actingAs($admin);
    }

    public function test_reviews_admin_crud_and_public_featured(): void
    {
        $id = $this->postJson('/api/v1/admin/reviews', ['customer_name' => 'Jane', 'rating' => 5, 'review_text' => 'Excellent', 'is_featured' => true])->assertCreated()->json('data.id');
        $this->patchJson("/api/v1/admin/reviews/{$id}", ['customer_name' => 'Jane', 'rating' => 5, 'review_text' => 'Excellent again', 'is_featured' => true])->assertOk();
        $this->getJson('/api/v1/public/reviews/featured')->assertOk()->assertJsonPath('data.0.customer_name', 'Jane');
        $this->deleteJson("/api/v1/admin/reviews/{$id}")->assertOk();
    }

    public function test_counters_team_partners_and_branches_admin_and_public_lists(): void
    {
        $counter = $this->postJson('/api/v1/admin/counters', ['key' => 'test_counter', 'label' => 'Test', 'value' => 10])->assertCreated()->json('data.id');
        $team = $this->postJson('/api/v1/admin/team-members', ['name' => 'Agent A', 'job_title' => 'Consultant'])->assertCreated()->json('data.id');
        $partner = $this->postJson('/api/v1/admin/partners', ['company_name' => 'Partner Co'])->assertCreated()->json('data.id');
        $branch = $this->postJson('/api/v1/admin/branches', ['name' => 'Dubai Branch', 'address' => 'Dubai UAE'])->assertCreated()->json('data.id');

        $this->getJson('/api/v1/public/counters')->assertOk();
        $this->getJson('/api/v1/public/team-members')->assertOk()->assertJsonFragment(['name' => 'Agent A']);
        $this->getJson('/api/v1/public/partners')->assertOk()->assertJsonFragment(['company_name' => 'Partner Co']);
        $this->getJson('/api/v1/public/branches')->assertOk()->assertJsonFragment(['name' => 'Dubai Branch']);

        $this->deleteJson("/api/v1/admin/counters/{$counter}")->assertOk();
        $this->deleteJson("/api/v1/admin/team-members/{$team}")->assertOk();
        $this->deleteJson("/api/v1/admin/partners/{$partner}")->assertOk();
        $this->deleteJson("/api/v1/admin/branches/{$branch}")->assertOk();
    }
}
