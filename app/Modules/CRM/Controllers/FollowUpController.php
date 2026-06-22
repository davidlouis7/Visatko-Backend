<?php

namespace App\Modules\CRM\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Consultations\Models\Consultation;
use App\Modules\CRM\Actions\ChangeFollowUpStatus;
use App\Modules\CRM\Actions\CreateFollowUp;
use App\Modules\CRM\Enums\FollowUpStatus;
use App\Modules\CRM\Models\FollowUp;
use App\Modules\CRM\Requests\StoreFollowUpRequest;
use App\Modules\CRM\Resources\FollowUpResource;
use App\Modules\VisaApplications\Models\VisaApplication;
use App\Support\Http\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class FollowUpController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        Gate::authorize('viewAny', FollowUp::class);
        FollowUp::query()->where('status', 'pending')->where('due_at', '<', now())->update(['status' => 'overdue']);
        $items = FollowUp::query()->when(request('assigned_to'), fn ($q, $v) => $q->where('assigned_to', $v))->when(request('status'), fn ($q, $v) => $q->where('status', $v))->when(request('due_from'), fn ($q, $v) => $q->where('due_at', '>=', $v))->when(request('due_to'), fn ($q, $v) => $q->where('due_at', '<=', $v))->orderBy('due_at')->paginate(min((int) request('per_page', 20), 100));

        return $this->paginated($items, FollowUpResource::collection($items->getCollection()));
    }

    public function forConsultation(StoreFollowUpRequest $request, Consultation $consultation, CreateFollowUp $action): JsonResponse
    {
        $item = $action->execute($consultation, $request->validated(), $request->user());

        return $this->success(FollowUpResource::make($item), 'Follow-up created successfully.', 201);
    }

    public function forApplication(StoreFollowUpRequest $request, VisaApplication $visaApplication, CreateFollowUp $action): JsonResponse
    {
        $item = $action->execute($visaApplication, $request->validated(), $request->user());

        return $this->success(FollowUpResource::make($item), 'Follow-up created successfully.', 201);
    }

    public function complete(FollowUp $followUp, ChangeFollowUpStatus $action): JsonResponse
    {
        Gate::authorize('update', $followUp);

        return $this->success(FollowUpResource::make($action->execute($followUp, FollowUpStatus::Completed, request()->user())), 'Follow-up completed successfully.');
    }

    public function cancel(FollowUp $followUp, ChangeFollowUpStatus $action): JsonResponse
    {
        Gate::authorize('update', $followUp);

        return $this->success(FollowUpResource::make($action->execute($followUp, FollowUpStatus::Cancelled, request()->user())), 'Follow-up cancelled successfully.');
    }
}
