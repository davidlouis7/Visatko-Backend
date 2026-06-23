<?php

namespace App\Modules\Refunds\Actions;

use App\Modules\CreditNotes\Enums\CreditNoteStatus;
use App\Modules\CreditNotes\Models\CreditNote;
use App\Modules\Finance\Services\FinanceSettings;
use App\Modules\Payments\Actions\RecordPaymentTransactionAction;
use App\Modules\Payments\Enums\PaymentProvider;
use App\Modules\Payments\Enums\PaymentTransactionStatus;
use App\Modules\Payments\Enums\PaymentTransactionType;
use App\Modules\Refunds\Enums\RefundRequestStatus;
use App\Modules\Refunds\Events\RefundProcessed;
use App\Modules\Refunds\Models\RefundRequest;
use Illuminate\Validation\ValidationException;

class ProcessRefundRequestAction
{
    public function __construct(
        private readonly RecordPaymentTransactionAction $recordPayment,
        private readonly FinanceSettings $settings,
    ) {}

    public function execute(RefundRequest $refund, ?string $notes = null): RefundRequest
    {
        if ($refund->status !== RefundRequestStatus::Approved) {
            throw ValidationException::withMessages(['refund' => 'Only approved refunds can be processed.']);
        }

        $transaction = $this->recordPayment->execute([
            'invoice_id' => $refund->invoice_id,
            'customer_id' => $refund->customer_id,
            'visa_application_id' => $refund->visa_application_id,
            'provider' => PaymentProvider::tryFrom($refund->provider) ?? PaymentProvider::Manual,
            'type' => PaymentTransactionType::Refund,
            'status' => PaymentTransactionStatus::Refunded,
            'currency' => $refund->currency,
            'amount' => $refund->amount,
            'notes' => $notes ?? $refund->internal_notes,
            'paid_at' => now(),
        ]);

        $creditNote = $refund->creditNote ?: $this->createIssuedCreditNote($refund);

        $refund->forceFill([
            'status' => RefundRequestStatus::Processed,
            'payment_transaction_id' => $transaction->id,
            'credit_note_id' => $creditNote->id,
            'processed_at' => now(),
            'internal_notes' => $notes ?? $refund->internal_notes,
        ])->save();
        event(new RefundProcessed($refund));

        return $refund->refresh();
    }

    private function createIssuedCreditNote(RefundRequest $refund): CreditNote
    {
        $invoice = $refund->invoice;
        $rate = (float) $invoice->vat_rate;
        $subtotal = $rate > 0 ? round((float) $refund->amount / (1 + ($rate / 100)), 2) : (float) $refund->amount;
        $vat = round((float) $refund->amount - $subtotal, 2);
        $creditNote = CreditNote::query()->create([
            'credit_note_number' => sprintf('%s-%s-%05d', $this->settings->get('credit_note_prefix', 'CN'), now()->format('Y'), CreditNote::withTrashed()->count() + 1),
            'invoice_id' => $invoice->id,
            'customer_id' => $refund->customer_id,
            'created_by' => $refund->approved_by,
            'status' => CreditNoteStatus::Issued,
            'reason' => $refund->reason,
            'subtotal' => $subtotal,
            'vat_amount' => $vat,
            'total' => $refund->amount,
            'issued_at' => now(),
            'meta' => ['refund_request_id' => $refund->id],
        ]);
        $creditNote->items()->create([
            'description' => 'Refund for '.$invoice->invoice_number,
            'quantity' => 1,
            'unit_price' => $subtotal,
            'vat_rate' => $rate,
            'vat_amount' => $vat,
            'line_total' => $refund->amount,
        ]);

        return $creditNote;
    }
}
