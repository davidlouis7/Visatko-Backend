<?php

namespace App\Modules\VisaServices\Resources;

use App\Modules\Countries\Resources\CountryResource;
use App\Modules\Media\Resources\MediaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VisaServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $t = $this->translationFor();

        return ['id' => $this->id, 'country' => CountryResource::make($this->whenLoaded('country')), 'thumbnail' => MediaResource::make($this->whenLoaded('thumbnail')), 'banner' => MediaResource::make($this->whenLoaded('banner')), 'gallery' => MediaResource::collection($this->whenLoaded('gallery')), 'price' => $this->price, 'discount_price' => $this->discount_price, 'currency' => $this->currency, 'processing_time' => $this->processing_time, 'visa_validity' => $this->visa_validity, 'stay_duration' => $this->stay_duration, 'is_featured' => $this->is_featured, 'is_active' => $this->is_active, 'sort_order' => $this->sort_order, 'title' => $t?->title, 'slug' => $t?->slug, 'short_description' => $t?->short_description, 'full_description' => $t?->full_description, 'requirements' => $t?->requirements, 'required_documents' => $t?->required_documents, 'terms_conditions' => $t?->terms_conditions, 'seo' => ['meta_title' => $t?->meta_title, 'meta_description' => $t?->meta_description, 'meta_keywords' => $t?->meta_keywords], 'translations' => $this->when($request->is('api/v1/admin/*'), fn () => $this->translations->mapWithKeys(fn ($item): array => [$item->language->code => collect($item)->except(['id', 'visa_service_id', 'language_id', 'language', 'created_at', 'updated_at'])->all()]))];
    }
}
