<?php

namespace App\Modules\Refunds\Actions;

use App\Models\User;
use App\Modules\Finance\Services\FinanceSettings;
use App\Modules\Invoices\Models\Invoice;
use App\Modules\Refunds\Enums\RefundRequestStatus;
use App\Modules\Refunds\Events\RefundRequested;
use App\Modules\Refunds\Models\RefundRequest;
use Illuminate\Validation\ValidationException;

class CreateRefundRequestAction
{
    public function __construct(private readonly FinanceSettings $settings) {}

    /** @param array<string, mixed> $data */
    public function execute(array $data, ?User $user = null): RefundRequest
    {
        $invoice = Invoice::query()->findOrFail($data['invoice_id']);
        $amount = round((float) $data['amount'], 2);
        if ($amount > (float) $invoice->amount_paid) {
            throw ValidationException::withMessages(['amount' => 'Refund amount cannot exceed paid amount.']);
        }

        $refund = RefundRequest::query()->create([
            'refund_number' => sprintf('%s-%s-%05d', $this->settings->get('refund_prefix', 'RF'), now()->format('Y'), RefundRequest::withTrashed()->count() + 1),
            'invoice_id' => $invoice->id,
            'customer_id' => $invoice->customer_id,
            'visa_application_id' => $invoice->visa_application_id,
            'requested_by' => $user?->id,
            'status' => RefundRequestStatus::Requested,
            'reason' => $data['reason'],
            'amount' => $amount,
            'currency' => $invoice->currency,
            'provider' => $data['provider'] ?? 'manual',
            'payment_transaction_id' => $data['payment_transaction_id'] ?? null,
            'requested_at' => now(),
            'internal_notes' => $data['internal_notes'] ?? null,
        ]);

        event(new RefundRequested($refund));
        activity('finance')->causedBy($user)->performedOn($refund)->log('Refund requested');

        return $refund;
    }
}
