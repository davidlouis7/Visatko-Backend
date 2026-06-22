<?php

namespace App\Modules\CRM\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TimelineResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'type' => $this->type, 'title' => $this->title, 'description' => $this->description, 'old_value' => $this->old_value, 'new_value' => $this->new_value, 'user' => $this->whenLoaded('user', fn () => $this->user ? ['id' => $this->user->id, 'name' => $this->user->name] : null), 'created_at' => $this->created_at?->toISOString()];
    }
}
