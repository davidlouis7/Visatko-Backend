<?php

namespace Tests\Feature\Consultations;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateConsultationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_persists_a_consultation_before_returning_whatsapp_redirect(): void
    {
        config(['services.whatsapp.number' => '+971501234567']);

        $response = $this->postJson('/api/v1/public/consultations', [
            'full_name' => 'Test Customer',
            'phone' => '+971500000000',
            'whatsapp_number' => '+971500000000',
            'email' => 'customer@example.com',
            'nationality' => 'Jordanian',
            'current_emirate' => 'Dubai',
            'are_you_residing_in_uae' => true,
            'monthly_salary_range' => 'above_5000',
            'salary_transferred_regularly' => true,
            'has_tenancy_contract' => true,
            'owns_car' => false,
            'has_previous_travel_history' => true,
            'previous_visa_refusal' => false,
            'expected_travel_date' => now()->addMonth()->toDateString(),
            'notes' => 'Call after 5pm.',
        ]);

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.consultation.status', 'new')
            ->assertJsonPath('data.consultation.full_name', 'Test Customer')
            ->assertJsonPath('data.whatsapp_url', fn (string $url) => str_starts_with($url, 'https://wa.me/971501234567?text='));

        $this->assertDatabaseHas('consultations', [
            'email' => 'customer@example.com',
            'status' => 'new',
        ]);
    }

    public function test_it_returns_the_standard_validation_error_shape(): void
    {
        $this->postJson('/api/v1/public/consultations', [])
            ->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors(['full_name', 'phone', 'monthly_salary_range']);
    }
}
