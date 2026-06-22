<?php

namespace App\Modules\VisaApplications\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\CRM\Actions\AddNote;
use App\Modules\CRM\Requests\StoreNoteRequest;
use App\Modules\CRM\Resources\NoteResource;
use App\Modules\CRM\Resources\TimelineResource;
use App\Modules\VisaApplications\Actions\AssignVisaApplicationAction;
use App\Modules\VisaApplications\Actions\ChangeVisaApplicationStatusAction;
use App\Modules\VisaApplications\Actions\CreateVisaApplicationAction;
use App\Modules\VisaApplications\Enums\ApplicationStatus;
use App\Modules\VisaApplications\Models\VisaApplication;
use App\Modules\VisaApplications\Requests\AssignVisaApplicationRequest;
use App\Modules\VisaApplications\Requests\ChangeVisaApplicationStatusRequest;
use App\Modules\VisaApplications\Requests\StoreVisaApplicationRequest;
use App\Modules\VisaApplications\Requests\UpdateVisaApplicationRequest;
use App\Modules\VisaApplications\Resources\VisaApplicationResource;
use App\Support\Http\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class VisaApplicationController extends Controller
{
    use ApiResponse;

    public function store(StoreVisaApplicationRequest $request, CreateVisaApplicationAction $action): JsonResponse
    {
        $application = $action->execute($request->validated());

        return $this->success(VisaApplicationResource::make($application), 'Visa application submitted successfully.', 201);
    }

    public function index(): JsonResponse
    {
        Gate::authorize('viewAny', VisaApplication::class);
        $search = request('search');
        $items = VisaApplication::query()->when(request('status'), fn ($q, $v) => $q->where('status', $v))->when(request('payment_status'), fn ($q, $v) => $q->where('payment_status', $v))->when(request('assigned_to'), fn ($q, $v) => $q->where('assigned_to', $v))->when(request('visa_service_id'), fn ($q, $v) => $q->where('visa_service_id', $v))->when(request('date_from'), fn ($q, $v) => $q->whereDate('created_at', '>=', $v))->when(request('date_to'), fn ($q, $v) => $q->whereDate('created_at', '<=', $v))->when($search, fn ($q) => $q->where(fn ($i) => $i->where('full_name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")->orWhere('application_number', 'like', "%{$search}%")))->latest()->paginate(min((int) request('per_page', 20), 100));

        return $this->paginated($items, VisaApplicationResource::collection($items->getCollection()));
    }

    public function show(VisaApplication $visaApplication): JsonResponse
    {
        Gate::authorize('view', $visaApplication);

        return $this->success(VisaApplicationResource::make($visaApplication->load('visaService.translations.language')));
    }

    public function update(UpdateVisaApplicationRequest $request, VisaApplication $visaApplication): JsonResponse
    {
        $visaApplication->update($request->validated());
        activity('admin')->causedBy($request->user())->performedOn($visaApplication)->log('Visa application updated');

        return $this->success(VisaApplicationResource::make($visaApplication), 'Visa application updated successfully.');
    }

    public function assign(AssignVisaApplicationRequest $request, VisaApplication $visaApplication, AssignVisaApplicationAction $action): JsonResponse
    {
        $application = $action->execute($visaApplication, User::query()->findOrFail($request->integer('assigned_to')), $request->user());

        return $this->success(VisaApplicationResource::make($application), 'Visa application assigned successfully.');
    }

    public function changeStatus(ChangeVisaApplicationStatusRequest $request, VisaApplication $visaApplication, ChangeVisaApplicationStatusAction $action): JsonResponse
    {
        $application = $action->execute($visaApplication, ApplicationStatus::from($request->string('status')->value()), $request->user(), $request->input('description'));

        return $this->success(VisaApplicationResource::make($application), 'Application status changed successfully.');
    }

    public function addNote(StoreNoteRequest $request, VisaApplication $visaApplication, AddNote $action): JsonResponse
    {
        Gate::authorize('view', $visaApplication);
        $note = $action->execute($visaApplication, $request->user(), $request->string('note')->value(), $request->boolean('is_private', true));

        return $this->success(NoteResource::make($note->load('user')), 'Note added successfully.', 201);
    }

    public function timeline(VisaApplication $visaApplication): JsonResponse
    {
        Gate::authorize('view', $visaApplication);

        return $this->success(TimelineResource::collection($visaApplication->timelines()->with('user')->latest('created_at')->get()));
    }
}
