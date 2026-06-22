<?php

namespace App\Modules\CRM\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FollowUpResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'subject_type' => $this->subject_type, 'subject_id' => $this->subject_id, 'assigned_to' => $this->assigned_to, 'created_by' => $this->created_by, 'due_at' => $this->due_at?->toISOString(), 'status' => $this->status->value, 'title' => $this->title, 'notes' => $this->notes, 'completed_at' => $this->completed_at?->toISOString(), 'created_at' => $this->created_at?->toISOString()];
    }
}
