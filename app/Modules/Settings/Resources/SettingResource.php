<?php

namespace App\Modules\Settings\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'group' => $this->group,
            'key' => $this->key,
            'value' => $this->is_encrypted ? null : $this->resolvedValue(),
            'is_configured' => $this->value !== '',
            'type' => $this->type,
            'is_public' => $this->is_public,
            'is_encrypted' => $this->is_encrypted,
        ];
    }
}
