<?php

namespace App\Modules\Blog\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $t = $this->translationFor();

        return ['id' => $this->id, 'name' => $t?->name, 'slug' => $t?->slug, 'description' => $t?->description, 'is_active' => $this->is_active, 'sort_order' => $this->sort_order, 'translations' => $this->when($request->is('api/v1/admin/*'), fn () => $this->translations->mapWithKeys(fn ($item): array => [$item->language->code => collect($item)->except(['id', 'blog_category_id', 'language_id', 'language', 'created_at', 'updated_at'])->all()]))];
    }
}
