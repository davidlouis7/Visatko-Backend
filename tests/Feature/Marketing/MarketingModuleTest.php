<?php

namespace Tests\Feature\Marketing;

use App\Models\User;
use App\Modules\Marketing\Actions\SendMetaConversionEventAction;
use App\Modules\Marketing\Enums\MarketingEventName;
use App\Modules\Marketing\Jobs\SendMetaConversionEventJob;
use App\Modules\Marketing\Models\MarketingEvent;
use App\Modules\Marketing\Services\MetaConversionApiService;
use Database\Seeders\MarketingSettingSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MarketingModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_tracking_settings_hide_secrets_and_meta_event_can_skip_safely(): void
    {
        Queue::fake();
        $this->seed([RolePermissionSeeder::class, MarketingSettingSeeder::class]);

        $this->getJson('/api/v1/public/tracking/settings')->assertOk()->assertJsonMissing(['meta_capi_token']);
        $event = app(SendMetaConversionEventAction::class)->execute(MarketingEventName::Lead, null, null, ['event_id' => 'lead-1']);
        $this->assertDatabaseHas('marketing_events', ['event_id' => 'lead-1', 'status' => 'pending']);

        (new SendMetaConversionEventJob($event->id))->handle(app(MetaConversionApiService::class));
        $this->assertSame('skipped', $event->refresh()->status->value);

        $admin = User::factory()->create();
        $admin->assignRole('Super Admin');
        Sanctum::actingAs($admin);
        $this->getJson('/api/v1/admin/marketing-events')->assertOk()->assertJsonPath('data.0.event_id', 'lead-1');
        $this->assertInstanceOf(MarketingEvent::class, $event);
    }
}
