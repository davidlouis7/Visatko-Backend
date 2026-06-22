<?php

namespace App\Modules\ApplicationDocuments\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\ApplicationDocuments\Actions\ReviewApplicationDocumentAction;
use App\Modules\ApplicationDocuments\Actions\UploadApplicationDocumentAction;
use App\Modules\ApplicationDocuments\Enums\DocumentStatus;
use App\Modules\ApplicationDocuments\Models\ApplicationDocument;
use App\Modules\ApplicationDocuments\Requests\ReviewApplicationDocumentRequest;
use App\Modules\ApplicationDocuments\Requests\UploadApplicationDocumentRequest;
use App\Modules\ApplicationDocuments\Resources\ApplicationDocumentResource;
use App\Modules\VisaApplications\Models\VisaApplication;
use App\Support\Http\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class ApplicationDocumentController extends Controller
{
    use ApiResponse;

    public function index(VisaApplication $visaApplication): JsonResponse
    {
        Gate::authorize('viewAny', ApplicationDocument::class);

        return $this->success(ApplicationDocumentResource::collection($visaApplication->documents()->with('media')->latest()->get()));
    }

    public function store(UploadApplicationDocumentRequest $request, VisaApplication $visaApplication, UploadApplicationDocumentAction $action): JsonResponse
    {
        $document = $action->execute($visaApplication, $request->file('file'), $request->string('document_type')->value(), $request->string('title')->value(), $request->user());

        return $this->success(ApplicationDocumentResource::make($document->load('media')), 'Document uploaded successfully.', 201);
    }

    public function review(ReviewApplicationDocumentRequest $request, ApplicationDocument $applicationDocument, ReviewApplicationDocumentAction $action): JsonResponse
    {
        $document = $action->execute($applicationDocument, DocumentStatus::from($request->string('status')->value()), $request->user(), $request->input('rejection_reason'));

        return $this->success(ApplicationDocumentResource::make($document->load('media')), 'Document reviewed successfully.');
    }
}
