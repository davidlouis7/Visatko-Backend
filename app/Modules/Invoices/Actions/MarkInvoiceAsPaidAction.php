<?php

namespace App\Modules\Invoices\Actions;

use App\Models\User;
use App\Modules\CRM\Actions\AddTimelineEntry;
use App\Modules\Invoices\Events\InvoicePaid;
use App\Modules\Invoices\Models\Invoice;
use App\Modules\Payments\Actions\RecordPaymentTransactionAction;
use App\Modules\Payments\Enums\PaymentProvider;
use App\Modules\Payments\Enums\PaymentTransactionStatus;
use App\Modules\Payments\Enums\PaymentTransactionType;
use Illuminate\Validation\ValidationException;

class MarkInvoiceAsPaidAction
{
    public function __construct(private readonly RecordPaymentTransactionAction $recordPayment, private readonly AddTimelineEntry $timeline) {}

    /** @param array<string, mixed> $data */
    public function execute(Invoice $invoice, array $data, ?User $user = null): Invoice
    {
        $amount = round((float) ($data['amount'] ?? $invoice->amount_due), 2);
        if ($amount <= 0 || $amount > (float) $invoice->amount_due) {
            throw ValidationException::withMessages(['amount' => 'Payment amount must be greater than zero and not exceed amount due.']);
        }

        $this->recordPayment->execute([
            'invoice_id' => $invoice->id,
            'customer_id' => $invoice->customer_id,
            'visa_application_id' => $invoice->visa_application_id,
            'provider' => PaymentProvider::Manual,
            'type' => PaymentTransactionType::Payment,
            'status' => PaymentTransactionStatus::Paid,
            'currency' => $invoice->currency,
            'amount' => $amount,
            'provider_reference' => $data['reference'] ?? null,
            'notes' => $data['notes'] ?? null,
            'paid_at' => now(),
        ]);

        $invoice->refresh();
        $this->timeline->execute($invoice, 'invoice.paid', 'Invoice payment recorded', $user);
        activity('finance')->causedBy($user)->performedOn($invoice)->log('Invoice manually marked paid');
        event(new InvoicePaid($invoice));

        return $invoice->load(['items', 'transactions']);
    }
}
