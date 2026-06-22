<?php

namespace App\Modules\Countries\Resources;

use App\Modules\Media\Resources\MediaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $translation = $this->translationFor();

        return ['id' => $this->id, 'code' => $this->code, 'flag' => MediaResource::make($this->whenLoaded('flag')), 'is_active' => $this->is_active, 'sort_order' => $this->sort_order, 'name' => $translation?->name, 'slug' => $translation?->slug, 'description' => $translation?->description, 'seo' => ['meta_title' => $translation?->meta_title, 'meta_description' => $translation?->meta_description, 'meta_keywords' => $translation?->meta_keywords], 'translations' => $this->when($request->is('api/v1/admin/*'), fn () => $this->translations->mapWithKeys(fn ($item): array => [$item->language->code => collect($item)->except(['id', 'country_id', 'language_id', 'language', 'created_at', 'updated_at'])->all()]))];
    }
}
