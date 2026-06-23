<?php

namespace App\Modules\Marketing\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketingEventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'event_name' => $this->event_name, 'event_id' => $this->event_id, 'status' => $this->status->value ?? $this->status, 'customer_id' => $this->customer_id, 'payload' => $this->payload, 'response' => $this->response, 'error_message' => $this->error_message, 'sent_at' => $this->sent_at?->toISOString(), 'created_at' => $this->created_at?->toISOString()];
    }
}
