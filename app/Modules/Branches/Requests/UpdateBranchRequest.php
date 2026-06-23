<?php

namespace App\Modules\Branches\Requests;

class UpdateBranchRequest extends StoreBranchRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('branches.update') ?? false;
    }
}
