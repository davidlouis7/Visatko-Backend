<?php

namespace App\Modules\CRM\Actions;

use App\Models\User;
use App\Modules\CRM\Enums\FollowUpStatus;
use App\Modules\CRM\Models\FollowUp;
use Illuminate\Database\Eloquent\Model;

class CreateFollowUp
{
    public function __construct(private AddTimelineEntry $timeline) {}

    public function execute(Model $subject, array $data, User $creator): FollowUp
    {
        $followUp = $subject->morphMany(FollowUp::class, 'subject')->create([...$data, 'created_by' => $creator->id, 'status' => FollowUpStatus::Pending]);
        $this->timeline->execute($subject, 'follow_up_created', 'Follow-up created', $creator, $followUp->title, null, ['follow_up_id' => $followUp->id]);
        activity('crm')->causedBy($creator)->performedOn($subject)->log('Follow-up created');

        return $followUp;
    }
}
