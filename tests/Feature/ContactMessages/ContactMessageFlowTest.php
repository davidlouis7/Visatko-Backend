<?php

namespace Tests\Feature\ContactMessages;

use App\Models\User;
use App\Modules\ContactMessages\Models\ContactMessage;
use Database\Seeders\EmailSettingSeeder;
use Database\Seeders\EmailTemplateSeeder;
use Database\Seeders\MarketingSettingSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ContactMessageFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_contact_message_and_admin_management(): void
    {
        Queue::fake();
        $this->seed([RolePermissionSeeder::class, EmailSettingSeeder::class, EmailTemplateSeeder::class, MarketingSettingSeeder::class]);

        $id = $this->postJson('/api/v1/public/contact-messages', [
            'full_name' => 'Lead Person',
            'email' => 'lead@example.com',
            'subject' => 'Visa help',
            'message' => 'Please call me.',
            'utm_source' => 'google',
        ])->assertCreated()->json('data.id');
        $this->assertDatabaseHas('contact_messages', ['id' => $id, 'utm_source' => 'google']);

        $admin = User::factory()->create();
        $admin->assignRole('Super Admin');
        Sanctum::actingAs($admin);
        $this->getJson('/api/v1/admin/contact-messages')->assertOk();
        $this->postJson("/api/v1/admin/contact-messages/{$id}/assign", ['assigned_to' => $admin->id])->assertOk()->assertJsonPath('data.assigned_to', $admin->id);
        $this->postJson("/api/v1/admin/contact-messages/{$id}/mark-read")->assertOk()->assertJsonPath('data.status', 'read');
        $this->postJson("/api/v1/admin/contact-messages/{$id}/close")->assertOk()->assertJsonPath('data.status', 'closed');
        $this->assertInstanceOf(ContactMessage::class, ContactMessage::query()->find($id));
    }
}
