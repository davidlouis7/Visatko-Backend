<?php

namespace App\Modules\Reviews\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'customer_name' => $this->customer_name, 'customer_country' => $this->customer_country, 'visa_service_id' => $this->visa_service_id, 'rating' => $this->rating, 'review_text' => $this->review_text, 'customer_image_media_id' => $this->customer_image_media_id, 'is_active' => $this->is_active, 'is_featured' => $this->is_featured, 'sort_order' => $this->sort_order];
    }
}
