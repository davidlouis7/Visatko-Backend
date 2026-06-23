<?php

namespace App\Modules\Marketing\Actions;

use App\Modules\Customers\Models\Customer;
use App\Modules\Marketing\Enums\MarketingEventName;
use App\Modules\Marketing\Enums\MarketingEventStatus;
use App\Modules\Marketing\Jobs\SendMetaConversionEventJob;
use App\Modules\Marketing\Models\MarketingEvent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SendMetaConversionEventAction
{
    /** @param array<string, mixed> $payload */
    public function execute(MarketingEventName|string $eventName, ?Model $related = null, ?Customer $customer = null, array $payload = []): MarketingEvent
    {
        $name = $eventName instanceof MarketingEventName ? $eventName->value : $eventName;
        $eventId = $payload['event_id'] ?? $payload['meta_event_id'] ?? Str::uuid()->toString();
        $event = MarketingEvent::query()->firstOrCreate(['event_id' => $eventId], [
            'event_name' => $name,
            'status' => MarketingEventStatus::Pending,
            'related_type' => $related?->getMorphClass(),
            'related_id' => $related?->getKey(),
            'customer_id' => $customer?->id,
            'email_hash' => $customer?->email ? hash('sha256', strtolower(trim($customer->email))) : null,
            'phone_hash' => $customer?->phone ? hash('sha256', preg_replace('/\D+/', '', $customer->phone)) : null,
            'ip_address' => $payload['ip_address'] ?? request()?->ip(),
            'user_agent' => $payload['user_agent'] ?? request()?->userAgent(),
            'fbc' => $payload['fbclid'] ?? null,
            'fbp' => $payload['fbp'] ?? null,
            'source_url' => $payload['landing_page'] ?? null,
            'payload' => $payload,
        ]);

        SendMetaConversionEventJob::dispatch($event->id);

        return $event;
    }
}
