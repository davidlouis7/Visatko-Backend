<?php

namespace App\Modules\VisaApplications\Actions;

use App\Models\User;
use App\Modules\CRM\Actions\AddTimelineEntry;
use App\Modules\VisaApplications\Enums\ApplicationStatus;
use App\Modules\VisaApplications\Events\VisaApplicationStatusChanged;
use App\Modules\VisaApplications\Models\VisaApplication;
use Illuminate\Validation\ValidationException;

class ChangeVisaApplicationStatusAction
{
    private const TRANSITIONS = ['new' => ['pending_documents', 'under_review', 'cancelled'], 'pending_documents' => ['under_review', 'cancelled'], 'under_review' => ['payment_pending', 'processing', 'rejected', 'cancelled'], 'payment_pending' => ['paid', 'cancelled'], 'paid' => ['processing', 'refunded'], 'processing' => ['approved', 'rejected', 'refunded'], 'approved' => ['completed', 'refunded'], 'rejected' => ['cancelled'], 'cancelled' => [], 'refunded' => [], 'completed' => []];

    public function __construct(private AddTimelineEntry $timeline) {}

    public function execute(VisaApplication $application, ApplicationStatus $status, User $actor, ?string $description = null): VisaApplication
    {
        $old = $application->status;
        if ($old !== $status && ! in_array($status->value, self::TRANSITIONS[$old->value] ?? [], true)) {
            throw ValidationException::withMessages(['status' => ["Cannot change status from {$old->value} to {$status->value}."]]);
        }
        $attributes = ['status' => $status];
        if ($status === ApplicationStatus::Completed) {
            $attributes['completed_at'] = now();
        }
        $application->update($attributes);
        $this->timeline->execute($application, 'application_status_changed', 'Application status changed', $actor, $description, ['status' => $old->value], ['status' => $status->value]);
        activity('admin')->causedBy($actor)->performedOn($application)->withProperties(['from' => $old->value, 'to' => $status->value])->log('Visa application status changed');
        event(new VisaApplicationStatusChanged($application, $old, $status));

        return $application;
    }
}
