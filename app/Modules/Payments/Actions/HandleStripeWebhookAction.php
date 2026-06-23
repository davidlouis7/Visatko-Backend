<?php

namespace App\Modules\Payments\Actions;

use App\Modules\Invoices\Models\Invoice;
use App\Modules\Payments\Enums\PaymentTransactionStatus;
use App\Modules\Payments\Events\PaymentFailed;
use App\Modules\Payments\Events\PaymentSucceeded;
use App\Modules\Payments\Models\PaymentTransaction;
use Illuminate\Support\Facades\DB;

class HandleStripeWebhookAction
{
    public function __construct(private readonly RecalculateInvoicePaymentStatusAction $recalculate) {}

    public function execute(object $event): ?PaymentTransaction
    {
        $eventId = (string) ($event->id ?? '');
        $type = (string) ($event->type ?? '');
        $object = $event->data->object ?? null;

        if ($eventId && PaymentTransaction::query()->where('webhook_event_id', $eventId)->exists()) {
            return PaymentTransaction::query()->where('webhook_event_id', $eventId)->first();
        }

        return DB::transaction(function () use ($event, $eventId, $type, $object): ?PaymentTransaction {
            $sessionId = $object->id ?? null;
            $invoiceNumber = $object->client_reference_id ?? ($object->metadata->invoice_number ?? null);
            $invoice = $invoiceNumber ? Invoice::query()->where('invoice_number', $invoiceNumber)->first() : null;
            $transaction = $sessionId ? PaymentTransaction::query()->where('provider_session_id', $sessionId)->first() : null;

            if (! $transaction && ! $invoice) {
                return null;
            }

            $transaction ??= PaymentTransaction::query()->create([
                'transaction_number' => sprintf('PT-%s-%05d', now()->format('Y'), PaymentTransaction::withTrashed()->count() + 1),
                'invoice_id' => $invoice->id,
                'visa_application_id' => $invoice->visa_application_id,
                'customer_id' => $invoice->customer_id,
                'provider' => 'stripe',
                'type' => 'payment',
                'currency' => $invoice->currency,
                'amount' => $invoice->amount_due,
            ]);

            $success = $type === 'checkout.session.completed' || str_contains($type, 'succeeded');
            $transaction->forceFill([
                'status' => $success ? PaymentTransactionStatus::Paid : PaymentTransactionStatus::Failed,
                'webhook_event_id' => $eventId ?: null,
                'provider_reference' => $object->payment_intent ?? $transaction->provider_reference,
                'provider_payment_intent_id' => $object->payment_intent ?? $transaction->provider_payment_intent_id,
                'raw_payload' => json_decode(json_encode($event), true),
                'paid_at' => $success ? now() : $transaction->paid_at,
                'failed_at' => $success ? $transaction->failed_at : now(),
            ])->save();

            if ($transaction->invoice) {
                $this->recalculate->execute($transaction->invoice);
            }

            event($success ? new PaymentSucceeded($transaction) : new PaymentFailed($transaction));

            return $transaction;
        });
    }
}
