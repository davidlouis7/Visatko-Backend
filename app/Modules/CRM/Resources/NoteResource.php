<?php

namespace App\Modules\CRM\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NoteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'note' => $this->note, 'is_private' => $this->is_private, 'user' => $this->whenLoaded('user', fn () => ['id' => $this->user->id, 'name' => $this->user->name]), 'created_at' => $this->created_at?->toISOString()];
    }
}
