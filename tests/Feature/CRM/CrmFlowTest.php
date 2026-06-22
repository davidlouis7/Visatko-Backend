<?php

namespace Tests\Feature\CRM;

use App\Models\User;
use App\Modules\Consultations\Models\Consultation;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CrmFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RolePermissionSeeder::class, LanguageSeeder::class]);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Super Admin');
        Sanctum::actingAs($this->admin);
    }

    public function test_notes_create_timeline_and_follow_up_can_be_completed(): void
    {
        $consultation = Consultation::factory()->create();
        $this->postJson("/api/v1/admin/consultations/{$consultation->id}/notes", ['note' => 'Call customer after 4 PM'])->assertCreated()->assertJsonPath('data.is_private', true);
        $followUp = $this->postJson("/api/v1/admin/consultations/{$consultation->id}/follow-ups", ['assigned_to' => $this->admin->id, 'due_at' => now()->addDay()->toISOString(), 'title' => 'Qualification call'])->assertCreated();
        $id = $followUp->json('data.id');
        $this->getJson('/api/v1/admin/follow-ups?assigned_to='.$this->admin->id)->assertOk()->assertJsonCount(1, 'data');
        $this->postJson("/api/v1/admin/follow-ups/{$id}/complete")->assertOk()->assertJsonPath('data.status', 'completed');
        $this->getJson("/api/v1/admin/consultations/{$consultation->id}/timeline")->assertOk()->assertJsonFragment(['type' => 'note_added']);
    }

    public function test_permissionless_user_cannot_access_crm_endpoints(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $this->getJson('/api/v1/admin/consultations')->assertForbidden();
        $this->getJson('/api/v1/admin/follow-ups')->assertForbidden();
    }
}
