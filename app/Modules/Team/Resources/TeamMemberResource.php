<?php

namespace App\Modules\Team\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamMemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'name' => $this->name, 'job_title' => $this->job_title, 'image_media_id' => $this->image_media_id, 'bio' => $this->bio, 'email' => $this->email, 'phone' => $this->phone, 'social_links' => $this->social_links, 'is_active' => $this->is_active, 'sort_order' => $this->sort_order];
    }
}
