<?php

namespace App\Modules\Payments\Actions;

use App\Modules\Invoices\Models\Invoice;
use App\Modules\Payments\Enums\PaymentTransactionStatus;
use App\Modules\Payments\Events\PaymentFailed;
use App\Modules\Payments\Events\PaymentSucceeded;
use App\Modules\Payments\Models\PaymentTransaction;
use Illuminate\Support\Facades\DB;

class HandleTabbyWebhookAction
{
    public function __construct(private readonly RecalculateInvoicePaymentStatusAction $recalculate) {}

    /** @param array<string, mixed> $payload */
    public function execute(array $payload): ?PaymentTransaction
    {
        $eventId = (string) ($payload['id'] ?? $payload['event_id'] ?? '');
        if ($eventId && PaymentTransaction::query()->where('webhook_event_id', $eventId)->exists()) {
            return PaymentTransaction::query()->where('webhook_event_id', $eventId)->first();
        }

        return DB::transaction(function () use ($payload, $eventId): ?PaymentTransaction {
            $payment = $payload['payment'] ?? $payload;
            $sessionId = $payment['id'] ?? $payload['payment_id'] ?? null;
            $invoiceNumber = $payment['order']['reference_id'] ?? $payload['invoice_number'] ?? null;
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
                'provider' => 'tabby',
                'type' => 'payment',
                'currency' => $invoice->currency,
                'amount' => $invoice->amount_due,
            ]);

            $status = strtolower((string) ($payment['status'] ?? $payload['status'] ?? ''));
            $success = in_array($status, ['authorized', 'paid', 'closed', 'captured'], true);
            $transaction->forceFill([
                'status' => $success ? PaymentTransactionStatus::Paid : PaymentTransactionStatus::Failed,
                'webhook_event_id' => $eventId ?: null,
                'provider_reference' => $sessionId,
                'raw_payload' => $payload,
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
