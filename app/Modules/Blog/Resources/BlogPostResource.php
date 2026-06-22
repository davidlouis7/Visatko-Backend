<?php

namespace App\Modules\Blog\Resources;

use App\Modules\Media\Resources\MediaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogPostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $t = $this->translationFor();

        return ['id' => $this->id, 'author' => $this->whenLoaded('author', fn () => ['id' => $this->author->id, 'name' => $this->author->name]), 'category' => BlogCategoryResource::make($this->whenLoaded('category')), 'tags' => BlogTagResource::collection($this->whenLoaded('tags')), 'thumbnail' => MediaResource::make($this->whenLoaded('thumbnail')), 'banner' => MediaResource::make($this->whenLoaded('banner')), 'is_published' => $this->is_published, 'is_featured' => $this->is_featured, 'published_at' => $this->published_at?->toISOString(), 'sort_order' => $this->sort_order, 'title' => $t?->title, 'slug' => $t?->slug, 'excerpt' => $t?->excerpt, 'content' => $t?->content, 'seo' => ['meta_title' => $t?->meta_title, 'meta_description' => $t?->meta_description, 'meta_keywords' => $t?->meta_keywords], 'translations' => $this->when($request->is('api/v1/admin/*'), fn () => $this->translations->mapWithKeys(fn ($item): array => [$item->language->code => collect($item)->except(['id', 'blog_post_id', 'language_id', 'language', 'created_at', 'updated_at'])->all()]))];
    }
}
