<?php

namespace App\Modules\Payments\Actions;

use App\Modules\Invoices\Models\Invoice;
use App\Modules\Payments\Enums\PaymentTransactionStatus;
use App\Modules\Payments\Models\PaymentTransaction;
use Illuminate\Support\Facades\DB;

class RecordPaymentTransactionAction
{
    public function __construct(private readonly RecalculateInvoicePaymentStatusAction $recalculate) {}

    /** @param array<string, mixed> $data */
    public function execute(array $data): PaymentTransaction
    {
        return DB::transaction(function () use ($data): PaymentTransaction {
            if (! empty($data['webhook_event_id'])) {
                $existing = PaymentTransaction::query()->where('webhook_event_id', $data['webhook_event_id'])->first();
                if ($existing) {
                    return $existing;
                }
            }

            $invoice = isset($data['invoice_id']) ? Invoice::query()->find($data['invoice_id']) : null;
            $transaction = PaymentTransaction::query()->create(array_merge([
                'transaction_number' => $this->nextNumber(),
                'customer_id' => $invoice?->customer_id,
                'visa_application_id' => $invoice?->visa_application_id,
                'currency' => $invoice?->currency ?? 'AED',
            ], $data));

            if ($invoice && in_array($transaction->status, [PaymentTransactionStatus::Paid, PaymentTransactionStatus::Refunded, PaymentTransactionStatus::Pending], true)) {
                $this->recalculate->execute($invoice);
            }

            return $transaction->load(['invoice', 'customer']);
        });
    }

    private function nextNumber(): string
    {
        return sprintf('PT-%s-%05d', now()->format('Y'), PaymentTransaction::withTrashed()->count() + 1);
    }
}
