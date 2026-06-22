<?php

namespace App\Modules\Media\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class MediaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->public_id,
            'original_name' => $this->original_name,
            'mime_type' => $this->mime_type,
            'size' => $this->size,
            'collection' => $this->collection,
            'visibility' => $this->visibility,
            'url' => $this->visibility === 'public' ? Storage::disk($this->disk)->url($this->path) : null,
            'download_url' => $this->visibility === 'private' ? route('api.v1.admin.media.download', $this->resource) : null,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
