<?php

namespace App\Modules\Consultations\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Consultations\Actions\ConvertConsultationToApplicationAction;
use App\Modules\Consultations\Actions\CreateConsultation;
use App\Modules\Consultations\Actions\GenerateConsultationWhatsAppMessageAction;
use App\Modules\Consultations\Models\Consultation;
use App\Modules\Consultations\Requests\AssignConsultationRequest;
use App\Modules\Consultations\Requests\ConvertConsultationRequest;
use App\Modules\Consultations\Requests\StoreConsultationRequest;
use App\Modules\Consultations\Requests\UpdateConsultationRequest;
use App\Modules\Consultations\Resources\ConsultationResource;
use App\Modules\CRM\Actions\AddNote;
use App\Modules\CRM\Actions\AddTimelineEntry;
use App\Modules\CRM\Requests\StoreNoteRequest;
use App\Modules\CRM\Resources\NoteResource;
use App\Modules\CRM\Resources\TimelineResource;
use App\Modules\VisaApplications\Resources\VisaApplicationResource;
use App\Support\Http\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class ConsultationController extends Controller
{
    use ApiResponse;

    public function store(StoreConsultationRequest $request, CreateConsultation $create, GenerateConsultationWhatsAppMessageAction $whatsApp): JsonResponse
    {
        $consultation = $create->execute($request->validated())->load(['destinationCountry.translations.language', 'preferredService.translations.language']);

        return $this->success(['consultation' => ConsultationResource::make($consultation), 'whatsapp_url' => $whatsApp->execute($consultation)], 'Consultation request submitted successfully.', 201);
    }

    public function index(): JsonResponse
    {
        Gate::authorize('viewAny', Consultation::class);
        $search = request('search');
        $items = Consultation::query()->when(request('status'), fn ($q, $status) => $q->where('status', $status))->when(request('assigned_to'), fn ($q, $id) => $q->where('assigned_to', $id))->when($search, fn ($q) => $q->where(fn ($i) => $i->where('full_name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")))->latest()->paginate(min((int) request('per_page', 20), 100));

        return $this->paginated($items, ConsultationResource::collection($items->getCollection()));
    }

    public function show(Consultation $consultation): JsonResponse
    {
        Gate::authorize('view', $consultation);

        return $this->success(ConsultationResource::make($consultation));
    }

    public function update(UpdateConsultationRequest $request, Consultation $consultation, AddTimelineEntry $timeline): JsonResponse
    {
        $oldStatus = $consultation->status;
        $consultation->update($request->validated());
        if ($request->has('status') && $oldStatus !== $consultation->status) {
            $timeline->execute($consultation, 'consultation_status_changed', 'Consultation status changed', $request->user(), null, ['status' => $oldStatus->value], ['status' => $consultation->status->value]);
        }
        activity('admin')->causedBy($request->user())->performedOn($consultation)->log('Consultation updated');

        return $this->success(ConsultationResource::make($consultation), 'Consultation updated successfully.');
    }

    public function assign(AssignConsultationRequest $request, Consultation $consultation, AddTimelineEntry $timeline): JsonResponse
    {
        $old = $consultation->assigned_to;
        $consultation->update(['assigned_to' => $request->integer('assigned_to')]);
        $timeline->execute($consultation, 'consultation_assigned', 'Consultation assigned', $request->user(), null, ['assigned_to' => $old], ['assigned_to' => $consultation->assigned_to]);
        activity('admin')->causedBy($request->user())->performedOn($consultation)->log('Consultation assigned');

        return $this->success(ConsultationResource::make($consultation), 'Consultation assigned successfully.');
    }

    public function addNote(StoreNoteRequest $request, Consultation $consultation, AddNote $action): JsonResponse
    {
        Gate::authorize('view', $consultation);
        $note = $action->execute($consultation, $request->user(), $request->string('note')->value(), $request->boolean('is_private', true));

        return $this->success(NoteResource::make($note->load('user')), 'Note added successfully.', 201);
    }

    public function convert(ConvertConsultationRequest $request, Consultation $consultation, ConvertConsultationToApplicationAction $action): JsonResponse
    {
        $application = $action->execute($consultation, $request->integer('visa_service_id'), $request->user());

        return $this->success(VisaApplicationResource::make($application), 'Consultation converted successfully.', 201);
    }

    public function timeline(Consultation $consultation): JsonResponse
    {
        Gate::authorize('view', $consultation);

        return $this->success(TimelineResource::collection($consultation->timelines()->with('user')->latest('created_at')->get()));
    }
}
