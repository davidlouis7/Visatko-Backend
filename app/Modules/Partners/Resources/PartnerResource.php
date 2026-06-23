<?php

namespace App\Modules\Partners\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartnerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'company_name' => $this->company_name, 'logo_media_id' => $this->logo_media_id, 'website_url' => $this->website_url, 'description' => $this->description, 'is_active' => $this->is_active, 'sort_order' => $this->sort_order];
    }
}
