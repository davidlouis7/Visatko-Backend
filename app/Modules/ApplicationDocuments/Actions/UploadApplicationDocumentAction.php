<?php

namespace App\Modules\ApplicationDocuments\Actions;

use App\Models\User;
use App\Modules\ApplicationDocuments\Enums\DocumentStatus;
use App\Modules\ApplicationDocuments\Events\DocumentUploaded;
use App\Modules\ApplicationDocuments\Models\ApplicationDocument;
use App\Modules\CRM\Actions\AddTimelineEntry;
use App\Modules\Media\Services\FileUploadService;
use App\Modules\VisaApplications\Models\VisaApplication;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class UploadApplicationDocumentAction
{
    public function __construct(private FileUploadService $files, private AddTimelineEntry $timeline) {}

    public function execute(VisaApplication $application, UploadedFile $file, string $type, string $title, ?User $user): ApplicationDocument
    {
        $media = $this->files->upload($file, 'application_documents', $user, ['application_number' => $application->application_number, 'document_type' => $type]);
        $document = DB::transaction(function () use ($application, $media, $type, $title, $user): ApplicationDocument {
            $document = ApplicationDocument::query()->create(['visa_application_id' => $application->id, 'customer_id' => $application->customer_id, 'media_id' => $media->id, 'document_type' => $type, 'title' => $title, 'status' => DocumentStatus::Uploaded, 'uploaded_by' => $user?->id]);
            $this->timeline->execute($application, 'document_uploaded', 'Document uploaded', $user, $title, null, ['document_id' => $document->id, 'type' => $type]);

            return $document;
        });
        if ($user) {
            activity('admin')->causedBy($user)->performedOn($application)->log('Application document uploaded');
        }
        event(new DocumentUploaded($document));

        return $document;
    }
}
