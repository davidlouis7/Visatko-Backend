<?php

namespace App\Modules\Invoices\Actions;

use App\Models\User;
use App\Modules\CRM\Actions\AddTimelineEntry;
use App\Modules\Finance\Services\FinanceSettings;
use App\Modules\Invoices\Enums\InvoiceStatus;
use App\Modules\Invoices\Events\InvoiceIssued;
use App\Modules\Invoices\Models\Invoice;
use Illuminate\Validation\ValidationException;

class IssueInvoiceAction
{
    public function __construct(private readonly FinanceSettings $settings, private readonly AddTimelineEntry $timeline) {}

    public function execute(Invoice $invoice, ?User $user = null): Invoice
    {
        if ($invoice->status !== InvoiceStatus::Draft) {
            throw ValidationException::withMessages(['invoice' => 'Only draft invoices can be issued.']);
        }

        $invoice->forceFill([
            'status' => InvoiceStatus::Issued,
            'issued_at' => now(),
            'due_at' => now()->addDays($this->settings->int('invoice_due_days')),
        ])->save();

        $this->timeline->execute($invoice, 'invoice.issued', 'Invoice issued', $user);
        activity('finance')->causedBy($user)->performedOn($invoice)->log('Invoice issued');
        event(new InvoiceIssued($invoice));

        return $invoice->refresh()->load(['customer', 'items']);
    }
}
