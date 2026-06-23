<?php

namespace App\Modules\Payments\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Invoices\Models\Invoice;
use App\Modules\Payments\Actions\CreateStripeCheckoutAction;
use App\Modules\Payments\Actions\HandleStripeWebhookAction;
use App\Modules\Payments\Resources\PaymentTransactionResource;
use App\Modules\Payments\Services\StripePaymentService;
use App\Support\Http\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StripeWebhookController extends Controller
{
    use ApiResponse;

    public function checkout(string $invoiceNumber, CreateStripeCheckoutAction $action): JsonResponse
    {
        $invoice = Invoice::query()->where('invoice_number', $invoiceNumber)->firstOrFail();
        $result = $action->execute($invoice);

        return $this->success([
            'checkout_url' => $result['checkout_url'],
            'transaction' => PaymentTransactionResource::make($result['transaction']),
        ], 'Stripe checkout session created.');
    }

    public function webhook(Request $request, StripePaymentService $stripe, HandleStripeWebhookAction $action): JsonResponse
    {
        $event = $stripe->constructWebhookEvent($request->getContent(), $request->header('Stripe-Signature'));
        $transaction = $action->execute($event);

        return $this->success($transaction ? PaymentTransactionResource::make($transaction) : null, 'Stripe webhook processed.');
    }
}
