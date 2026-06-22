<?php

namespace Tests\Feature\ApplicationDocuments;

use App\Models\User;
use App\Modules\VisaApplications\Models\VisaApplication;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApplicationDocumentFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_upload_is_private_and_admin_can_review_document(): void
    {
        $this->seed([RolePermissionSeeder::class, LanguageSeeder::class]);
        Storage::fake('local');
        $application = VisaApplication::factory()->create();
        $response = $this->postJson("/api/v1/public/visa-applications/{$application->id}/documents", ['file' => UploadedFile::fake()->create('passport.pdf', 100, 'application/pdf'), 'document_type' => 'passport', 'title' => 'Passport copy'])->assertCreated()->assertJsonPath('data.status', 'uploaded')->assertJsonPath('data.media.visibility', 'private');
        $documentId = $response->json('data.id');
        $admin = User::factory()->create();
        $admin->assignRole('Super Admin');
        Sanctum::actingAs($admin);
        $this->getJson("/api/v1/admin/visa-applications/{$application->id}/documents")->assertOk()->assertJsonCount(1, 'data');
        $this->postJson("/api/v1/admin/application-documents/{$documentId}/review", ['status' => 'rejected', 'rejection_reason' => 'Passport image is blurry'])->assertOk()->assertJsonPath('data.status', 'rejected');
        $this->assertDatabaseHas('timelines', ['subject_type' => 'visa_application', 'subject_id' => $application->id, 'type' => 'document_rejected']);
    }
}
