<?php

namespace App\Modules\Blog\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogTagResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $t = $this->translationFor();

        return ['id' => $this->id, 'name' => $t?->name, 'slug' => $t?->slug, 'is_active' => $this->is_active, 'translations' => $this->when($request->is('api/v1/admin/*'), fn () => $this->translations->mapWithKeys(fn ($item): array => [$item->language->code => ['name' => $item->name, 'slug' => $item->slug]]))];
    }
}
