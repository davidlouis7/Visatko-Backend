<?php

namespace App\Modules\ApplicationDocuments\Resources;

use App\Modules\Media\Resources\MediaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationDocumentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'visa_application_id' => $this->visa_application_id, 'document_type' => $this->document_type->value, 'title' => $this->title, 'status' => $this->status->value, 'rejection_reason' => $this->rejection_reason, 'media' => MediaResource::make($this->whenLoaded('media')), 'uploaded_by' => $this->uploaded_by, 'reviewed_by' => $this->reviewed_by, 'reviewed_at' => $this->reviewed_at?->toISOString(), 'created_at' => $this->created_at?->toISOString()];
    }
}
