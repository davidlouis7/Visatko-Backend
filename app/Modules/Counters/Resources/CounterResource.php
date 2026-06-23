<?php

namespace App\Modules\Counters\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CounterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'key' => $this->key, 'label' => $this->label, 'value' => $this->value, 'suffix' => $this->suffix, 'is_active' => $this->is_active, 'sort_order' => $this->sort_order];
    }
}
