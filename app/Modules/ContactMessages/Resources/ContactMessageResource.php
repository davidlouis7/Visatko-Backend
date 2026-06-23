<?php

namespace App\Modules\ContactMessages\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactMessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'full_name' => $this->full_name, 'email' => $this->email, 'phone' => $this->phone, 'subject' => $this->subject, 'message' => $this->message, 'status' => $this->status->value ?? $this->status, 'assigned_to' => $this->assigned_to, 'source' => $this->source, 'utm_source' => $this->utm_source, 'created_at' => $this->created_at?->toISOString()];
    }
}
