<?php

namespace App\Modules\CRM\Actions;

use App\Models\User;
use App\Modules\CRM\Enums\FollowUpStatus;
use App\Modules\CRM\Models\FollowUp;
use Illuminate\Validation\ValidationException;

class ChangeFollowUpStatus
{
    public function execute(FollowUp $followUp, FollowUpStatus $status, User $actor): FollowUp
    {
        if (! in_array($status, [FollowUpStatus::Completed, FollowUpStatus::Cancelled], true)) {
            throw ValidationException::withMessages(['status' => ['Invalid follow-up status.']]);
        }
        if ($followUp->status !== FollowUpStatus::Pending && $followUp->status !== FollowUpStatus::Overdue) {
            throw ValidationException::withMessages(['status' => ['This follow-up is already closed.']]);
        }
        $followUp->update(['status' => $status, 'completed_at' => $status === FollowUpStatus::Completed ? now() : null]);
        activity('crm')->causedBy($actor)->performedOn($followUp)->log('Follow-up status changed');

        return $followUp;
    }
}
