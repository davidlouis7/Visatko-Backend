<?php

namespace App\Modules\Refunds\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Refunds\Actions\ApproveRefundRequestAction;
use App\Modules\Refunds\Actions\CreateRefundRequestAction;
use App\Modules\Refunds\Actions\ProcessRefundRequestAction;
use App\Modules\Refunds\Actions\RejectRefundRequestAction;
use App\Modules\Refunds\Models\RefundRequest;
use App\Modules\Refunds\Requests\ReviewRefundRequestRequest;
use App\Modules\Refunds\Requests\StoreRefundRequestRequest;
use App\Modules\Refunds\Resources\RefundRequestResource;
use App\Support\Http\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RefundRequestController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', RefundRequest::class);
        $paginator = RefundRequest::query()->with('invoice')->latest()->paginate((int) $request->integer('per_page', 15));

        return $this->paginated($paginator, RefundRequestResource::collection($paginator));
    }

    public function store(StoreRefundRequestRequest $request, CreateRefundRequestAction $action): JsonResponse
    {
        return $this->success(RefundRequestResource::make($action->execute($request->validated(), $request->user())), 'Refund request created.', 201);
    }

    public function show(RefundRequest $refundRequest): JsonResponse
    {
        Gate::authorize('view', $refundRequest);

        return $this->success(RefundRequestResource::make($refundRequest));
    }

    public function approve(ReviewRefundRequestRequest $request, RefundRequest $refundRequest, ApproveRefundRequestAction $action): JsonResponse
    {
        return $this->success(RefundRequestResource::make($action->execute($refundRequest, $request->user(), $request->input('internal_notes'))), 'Refund approved.');
    }

    public function reject(ReviewRefundRequestRequest $request, RefundRequest $refundRequest, RejectRefundRequestAction $action): JsonResponse
    {
        return $this->success(RefundRequestResource::make($action->execute($refundRequest, $request->user(), $request->input('internal_notes'))), 'Refund rejected.');
    }

    public function process(ReviewRefundRequestRequest $request, RefundRequest $refundRequest, ProcessRefundRequestAction $action): JsonResponse
    {
        return $this->success(RefundRequestResource::make($action->execute($refundRequest, $request->input('internal_notes'))), 'Refund processed.');
    }
}
