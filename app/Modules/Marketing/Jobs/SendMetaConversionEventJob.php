<?php

namespace App\Modules\Marketing\Jobs;

use App\Modules\Marketing\Enums\MarketingEventStatus;
use App\Modules\Marketing\Models\MarketingEvent;
use App\Modules\Marketing\Services\MetaConversionApiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class SendMetaConversionEventJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $marketingEventId) {}

    public function handle(MetaConversionApiService $meta): void
    {
        $event = MarketingEvent::query()->findOrFail($this->marketingEventId);

        if (! $meta->enabled()) {
            $event->forceFill(['status' => MarketingEventStatus::Skipped, 'error_message' => 'Meta CAPI disabled or missing token/pixel.'])->save();

            return;
        }

        try {
            $payload = [
                'event_name' => $event->event_name,
                'event_time' => now()->timestamp,
                'event_id' => $event->event_id,
                'action_source' => 'website',
                'event_source_url' => $event->source_url,
                'user_data' => array_filter([
                    'em' => $event->email_hash ? [$event->email_hash] : null,
                    'ph' => $event->phone_hash ? [$event->phone_hash] : null,
                    'client_ip_address' => $event->ip_address,
                    'client_user_agent' => $event->user_agent,
                    'fbc' => $event->fbc,
                    'fbp' => $event->fbp,
                ]),
                'custom_data' => $event->payload ?? [],
            ];
            $event->forceFill(['response' => $meta->send($payload), 'status' => MarketingEventStatus::Sent, 'sent_at' => now(), 'error_message' => null])->save();
        } catch (Throwable $exception) {
            $event->forceFill(['status' => MarketingEventStatus::Failed, 'error_message' => $exception->getMessage()])->save();
        }
    }
}
