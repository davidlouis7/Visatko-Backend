<?php

namespace App\Modules\Partners\Requests;

class UpdatePartnerRequest extends StorePartnerRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('partners.update') ?? false;
    }
}
