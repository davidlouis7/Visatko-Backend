<?php

namespace App\Modules\Payments\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Finance\Services\FinanceSettings;
use App\Modules\Invoices\Models\Invoice;
use App\Modules\Payments\Actions\CreateBankTransferPaymentAction;
use App\Modules\Payments\Actions\ReviewBankTransferPaymentAction;
use App\Modules\Payments\Enums\PaymentProvider;
use App\Modules\Payments\Enums\PaymentTransactionStatus;
use App\Modules\Payments\Models\PaymentTransaction;
use App\Modules\Payments\Requests\BankTransferUploadRequest;
use App\Modules\Payments\Requests\ReviewBankTransferRequest;
use App\Modules\Payments\Resources\PaymentTransactionResource;
use App\Support\Http\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BankTransferPaymentController extends Controller
{
    use ApiResponse;

    public function store(string $invoiceNumber, BankTransferUploadRequest $request, CreateBankTransferPaymentAction $action, FinanceSettings $settings): JsonResponse
    {
        $invoice = Invoice::query()->where('invoice_number', $invoiceNumber)->firstOrFail();
        $transaction = $action->execute($invoice, $request->file('receipt'), $request->validated());

        return $this->success([
            'transaction' => PaymentTransactionResource::make($transaction),
            'bank_transfer' => $settings->publicPaymentConfig()['bank_transfer'],
        ], 'Bank transfer receipt uploaded for review.', 201);
    }

    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', PaymentTransaction::class);
        $paginator = PaymentTransaction::query()
            ->where('provider', PaymentProvider::BankTransfer->value)
            ->where('status', PaymentTransactionStatus::PendingReview->value)
            ->latest()->paginate((int) $request->integer('per_page', 15));

        return $this->paginated($paginator, PaymentTransactionResource::collection($paginator));
    }

    public function approve(ReviewBankTransferRequest $request, PaymentTransaction $transaction, ReviewBankTransferPaymentAction $action): JsonResponse
    {
        return $this->success(PaymentTransactionResource::make($action->approve($transaction, $request->user(), $request->input('notes'))), 'Bank transfer approved.');
    }

    public function reject(ReviewBankTransferRequest $request, PaymentTransaction $transaction, ReviewBankTransferPaymentAction $action): JsonResponse
    {
        return $this->success(PaymentTransactionResource::make($action->reject($transaction, $request->user(), $request->input('notes'))), 'Bank transfer rejected.');
    }
}
