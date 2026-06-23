<?php

namespace App\Modules\Emails\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmailLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'template_key' => $this->template_key,
            'subject' => $this->subject,
            'recipient_email' => $this->recipient_email,
            'recipient_name' => $this->recipient_name,
            'status' => $this->status->value ?? $this->status,
            'payload' => $this->payload,
            'error_message' => $this->error_message,
            'sent_at' => $this->sent_at?->toISOString(),
            'failed_at' => $this->failed_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
