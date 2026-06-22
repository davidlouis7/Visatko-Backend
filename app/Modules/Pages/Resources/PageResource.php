<?php

namespace App\Modules\Pages\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $t = $this->translationFor();

        return ['id' => $this->id, 'key' => $this->key, 'is_active' => $this->is_active, 'title' => $t?->title, 'slug' => $t?->slug, 'content' => $t?->content, 'seo' => ['meta_title' => $t?->meta_title, 'meta_description' => $t?->meta_description, 'meta_keywords' => $t?->meta_keywords], 'translations' => $this->when($request->is('api/v1/admin/*'), fn () => $this->translations->mapWithKeys(fn ($item): array => [$item->language->code => collect($item)->except(['id', 'page_id', 'language_id', 'language', 'created_at', 'updated_at'])->all()]))];
    }
}
