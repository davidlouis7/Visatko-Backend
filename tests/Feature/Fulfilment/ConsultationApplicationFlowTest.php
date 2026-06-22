<?php

namespace Tests\Feature\Fulfilment;

use App\Models\User;
use App\Modules\Consultations\Models\Consultation;
use App\Modules\Countries\Models\Country;
use App\Modules\Customers\Models\Customer;
use App\Modules\VisaServices\Models\VisaService;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ConsultationApplicationFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private VisaService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RolePermissionSeeder::class, LanguageSeeder::class]);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Super Admin');
        $this->service = VisaService::factory()->create(['country_id' => Country::factory()->create()->id]);
    }

    public function test_public_consultation_reuses_customer_returns_whatsapp_and_creates_timeline(): void
    {
        config(['services.whatsapp.number' => '+971501234567']);
        Customer::factory()->create(['phone' => '+971500000001', 'email' => 'lead@example.com']);
        $response = $this->postJson('/api/v1/public/consultations', $this->consultationPayload())
            ->assertCreated()->assertJsonPath('data.consultation.status', 'new')
            ->assertJsonPath('data.whatsapp_url', fn (string $url): bool => str_starts_with($url, 'https://wa.me/971501234567?text='));
        $this->assertDatabaseCount('customers', 1);
        $this->assertDatabaseHas('timelines', ['subject_type' => 'consultation', 'type' => 'consultation_created']);
        $this->assertNotNull($response->json('data.consultation.public_id'));
    }

    public function test_admin_lists_updates_assigns_and_converts_consultation(): void
    {
        $consultation = $this->createConsultation();
        Sanctum::actingAs($this->admin);
        $assignee = User::factory()->create();
        $this->getJson('/api/v1/admin/consultations?status=new')->assertOk()->assertJsonCount(1, 'data');
        $this->getJson("/api/v1/admin/consultations/{$consultation->id}")->assertOk();
        $this->patchJson("/api/v1/admin/consultations/{$consultation->id}", ['status' => 'qualified'])->assertOk()->assertJsonPath('data.status', 'qualified');
        $this->postJson("/api/v1/admin/consultations/{$consultation->id}/assign", ['assigned_to' => $assignee->id])->assertOk()->assertJsonPath('data.assigned_to', $assignee->id);
        $response = $this->postJson("/api/v1/admin/consultations/{$consultation->id}/convert-to-application", ['visa_service_id' => $this->service->id])->assertCreated();
        $this->assertDatabaseHas('visa_applications', ['id' => $response->json('data.id'), 'consultation_id' => $consultation->id]);
        $this->assertDatabaseHas('consultations', ['id' => $consultation->id, 'status' => 'converted_to_application']);
    }

    public function test_public_application_and_admin_update_assign_status_and_filters(): void
    {
        $response = $this->postJson('/api/v1/public/visa-applications', ['visa_service_id' => $this->service->id, 'full_name' => 'Applicant One', 'email' => 'applicant@example.com', 'phone' => '+971509999999', 'nationality' => 'Jordanian', 'travel_date' => now()->addMonth()->toDateString()])->assertCreated()->assertJsonPath('data.status', 'new');
        $id = $response->json('data.id');
        $this->assertMatchesRegularExpression('/^VSA-\d{6}-[A-Z0-9]{8}$/', $response->json('data.application_number'));
        Sanctum::actingAs($this->admin);
        $assignee = User::factory()->create();
        $this->getJson('/api/v1/admin/visa-applications?search=Applicant&status=new')->assertOk()->assertJsonCount(1, 'data');
        $this->patchJson("/api/v1/admin/visa-applications/{$id}", ['internal_notes' => 'Check travel history'])->assertOk();
        $this->postJson("/api/v1/admin/visa-applications/{$id}/assign", ['assigned_to' => $assignee->id])->assertOk();
        $this->postJson("/api/v1/admin/visa-applications/{$id}/change-status", ['status' => 'under_review'])->assertOk()->assertJsonPath('data.status', 'under_review');
        $this->assertDatabaseHas('timelines', ['subject_type' => 'visa_application', 'subject_id' => $id, 'type' => 'application_status_changed']);
    }

    private function createConsultation(): Consultation
    {
        return Consultation::factory()->create(['preferred_visa_service_id' => $this->service->id]);
    }

    private function consultationPayload(): array
    {
        return ['full_name' => 'Lead Person', 'phone' => '+971500000001', 'whatsapp_number' => '+971500000001', 'email' => 'lead@example.com', 'nationality' => 'Jordanian', 'current_emirate' => 'Dubai', 'preferred_visa_service_id' => $this->service->id, 'are_you_residing_in_uae' => true, 'monthly_salary_range' => 'above_5000', 'salary_transferred_regularly' => true, 'has_tenancy_contract' => true, 'owns_car' => false, 'has_previous_travel_history' => true, 'previous_visa_refusal' => false, 'expected_travel_date' => now()->addMonth()->toDateString(), 'source' => 'website'];
    }
}
