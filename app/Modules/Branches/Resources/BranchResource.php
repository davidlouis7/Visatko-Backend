<?php

namespace App\Modules\Branches\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'name' => $this->name, 'address' => $this->address, 'phone_numbers' => $this->phone_numbers, 'whatsapp_number' => $this->whatsapp_number, 'email' => $this->email, 'google_maps_url' => $this->google_maps_url, 'working_hours' => $this->working_hours, 'emirate' => $this->emirate, 'city' => $this->city, 'is_active' => $this->is_active, 'sort_order' => $this->sort_order];
    }
}
