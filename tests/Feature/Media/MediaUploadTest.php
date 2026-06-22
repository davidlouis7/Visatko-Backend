<?php

namespace Tests\Feature\Media;

use App\Models\User;
use App\Modules\Media\Models\Media;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MediaUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole('Super Admin');
        Sanctum::actingAs($admin);
    }

    public function test_marketing_image_is_validated_stored_and_returned_as_public_media(): void
    {
        Storage::fake('public');

        $response = $this->postJson('/api/v1/admin/media', [
            'file' => UploadedFile::fake()->image('team-member.jpg', 600, 600),
            'collection' => 'team_images',
        ])->assertCreated()
            ->assertJsonPath('data.visibility', 'public')
            ->assertJsonPath('data.collection', 'team_images');

        $path = Media::query()->sole()->path;
        Storage::disk('public')->assertExists($path);
        $this->assertDatabaseHas('activity_log', ['description' => 'Media uploaded']);
    }

    public function test_identity_document_is_forced_to_private_storage(): void
    {
        Storage::fake('local');

        $this->postJson('/api/v1/admin/media', [
            'file' => UploadedFile::fake()->create('passport.pdf', 200, 'application/pdf'),
            'collection' => 'passports',
        ])->assertCreated()
            ->assertJsonPath('data.visibility', 'private')
            ->assertJsonPath('data.url', null)
            ->assertJsonStructure(['data' => ['download_url']]);

        $media = Media::query()->sole();
        Storage::disk('local')->assertExists($media->path);
    }

    public function test_executable_upload_is_rejected(): void
    {
        $this->postJson('/api/v1/admin/media', [
            'file' => UploadedFile::fake()->create('malware.php', 10, 'application/x-php'),
            'collection' => 'service_images',
        ])->assertUnprocessable()->assertJsonValidationErrors('file');
    }
}
