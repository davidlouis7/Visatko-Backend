<?php

namespace App\Modules\Team\Requests;

class UpdateTeamMemberRequest extends StoreTeamMemberRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('team.update') ?? false;
    }
}
