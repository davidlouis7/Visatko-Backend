<?php

namespace App\Modules\Payments\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Invoices\Models\Invoice;
use App\Modules\Payments\Actions\CreateTabbyPaymentAction;
use App\Modules\Payments\Actions\HandleTabbyWebhookAction;
use App\Modules\Payments\Resources\PaymentTransactionResource;
use App\Modules\Payments\Services\TabbyPaymentService;
use App\Support\Http\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TabbyWebhookController extends Controller
{
    use ApiResponse;

    public function checkout(string $invoiceNumber, CreateTabbyPaymentAction $action): JsonResponse
    {
        $invoice = Invoice::query()->where('invoice_number', $invoiceNumber)->firstOrFail();
        $result = $action->execute($invoice);

        return $this->success([
            'payment_url' => $result['payment_url'],
            'session' => $result['session'],
            'transaction' => PaymentTransactionResource::make($result['transaction']),
        ], 'Tabby payment session created.');
    }

    public function webhook(Request $request, TabbyPaymentService $tabby, HandleTabbyWebhookAction $action): JsonResponse
    {
        abort_unless($tabby->verifyWebhook($request->getContent(), $request->header('X-Tabby-Signature')), 401);
        $transaction = $action->execute($request->json()->all());

        return $this->success($transaction ? PaymentTransactionResource::make($transaction) : null, 'Tabby webhook processed.');
    }
}
