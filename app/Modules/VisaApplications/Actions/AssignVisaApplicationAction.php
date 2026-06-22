<?php

namespace App\Modules\VisaApplications\Actions;

use App\Models\User;
use App\Modules\CRM\Actions\AddTimelineEntry;
use App\Modules\VisaApplications\Models\VisaApplication;

class AssignVisaApplicationAction
{
    public function __construct(private AddTimelineEntry $timeline) {}

    public function execute(VisaApplication $application, User $assignee, User $actor): VisaApplication
    {
        $old = $application->assigned_to;
        $application->update(['assigned_to' => $assignee->id]);
        $this->timeline->execute($application, 'application_assigned', 'Application assigned', $actor, null, ['assigned_to' => $old], ['assigned_to' => $assignee->id]);
        activity('admin')->causedBy($actor)->performedOn($application)->log('Visa application assigned');

        return $application;
    }
}
