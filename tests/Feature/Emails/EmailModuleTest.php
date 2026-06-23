<?php

namespace Tests\Feature\Emails;

use App\Models\User;
use App\Modules\Emails\Actions\SendTransactionalEmailAction;
use App\Modules\Emails\Models\EmailTemplate;
use Database\Seeders\EmailSettingSeeder;
use Database\Seeders\EmailTemplateSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EmailModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_templates_seed_queue_and_admin_update_logs_are_protected(): void
    {
        Queue::fake();
        $this->seed([RolePermissionSeeder::class, EmailSettingSeeder::class, EmailTemplateSeeder::class]);

        $this->assertDatabaseHas('email_templates', ['key' => 'invoice_issued_customer']);
        app(SendTransactionalEmailAction::class)->execute('invoice_issued_customer', 'client@example.com', 'Client', ['invoice_number' => 'INV-1', 'amount_due' => 'AED 100']);
        $this->assertDatabaseHas('email_logs', ['recipient_email' => 'client@example.com', 'status' => 'queued']);

        $admin = User::factory()->create();
        $admin->assignRole('Super Admin');
        Sanctum::actingAs($admin);
        $template = EmailTemplate::query()->where('key', 'invoice_issued_customer')->first();
        $this->patchJson("/api/v1/admin/email-templates/{$template->key}", ['subject' => 'Updated {{invoice_number}}'])->assertOk()->assertJsonPath('data.subject', 'Updated {{invoice_number}}');
        $this->getJson('/api/v1/admin/email-logs')->assertOk();

        Sanctum::actingAs(User::factory()->create());
        $this->getJson('/api/v1/admin/email-logs')->assertForbidden();
    }
}
