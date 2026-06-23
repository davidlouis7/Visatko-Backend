<?php

namespace App\Modules\ApplicationDocuments\Actions;

use App\Models\User;
use App\Modules\ApplicationDocuments\Enums\DocumentStatus;
use App\Modules\ApplicationDocuments\Events\DocumentRejected;
use App\Modules\ApplicationDocuments\Models\ApplicationDocument;
use App\Modules\CRM\Actions\AddTimelineEntry;
use Illuminate\Validation\ValidationException;

class ReviewApplicationDocumentAction
{
    public function __construct(private AddTimelineEntry $timeline) {}

    public function execute(ApplicationDocument $document, DocumentStatus $status, User $reviewer, ?string $reason): ApplicationDocument
    {
        if (! in_array($status, [DocumentStatus::Accepted, DocumentStatus::Rejected, DocumentStatus::NeedsReupload], true)) {
            throw ValidationException::withMessages(['status' => ['Invalid review status.']]);
        }
        if (in_array($status, [DocumentStatus::Rejected, DocumentStatus::NeedsReupload], true) && ! $reason) {
            throw ValidationException::withMessages(['rejection_reason' => ['A rejection reason is required.']]);
        }
        $old = $document->status;
        $document->update(['status' => $status, 'rejection_reason' => $reason, 'reviewed_by' => $reviewer->id, 'reviewed_at' => now()]);
        $type = $status === DocumentStatus::Accepted ? 'document_approved' : 'document_rejected';
        $this->timeline->execute($document->application, $type, $status === DocumentStatus::Accepted ? 'Document approved' : 'Document rejected', $reviewer, $reason, ['status' => $old->value], ['status' => $status->value]);
        activity('admin')->causedBy($reviewer)->performedOn($document->application)->log('Application document reviewed');
        if (in_array($status, [DocumentStatus::Rejected, DocumentStatus::NeedsReupload], true)) {
            event(new DocumentRejected($document->refresh()));
        }

        return $document;
    }
}
