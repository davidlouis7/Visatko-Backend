<?php

namespace App\Modules\Payments\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Payments\Models\PaymentTransaction;
use App\Modules\Payments\Resources\PaymentTransactionResource;
use App\Support\Http\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PaymentTransactionController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', PaymentTransaction::class);
        $paginator = PaymentTransaction::query()->with(['invoice', 'customer'])->latest()->paginate((int) $request->integer('per_page', 15));

        return $this->paginated($paginator, PaymentTransactionResource::collection($paginator));
    }

    public function show(PaymentTransaction $transaction): JsonResponse
    {
        Gate::authorize('view', $transaction);

        return $this->success(PaymentTransactionResource::make($transaction->load(['invoice', 'customer'])));
    }
}
