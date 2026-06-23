<?php

namespace App\Modules\Invoices\Actions;

use App\Models\User;
use App\Modules\CRM\Actions\AddTimelineEntry;
use App\Modules\Invoices\Enums\InvoiceStatus;
use App\Modules\Invoices\Models\Invoice;
use Illuminate\Validation\ValidationException;

class MarkInvoiceAsSentAction
{
    public function __construct(private readonly AddTimelineEntry $timeline) {}

    public function execute(Invoice $invoice, ?User $user = null): Invoice
    {
        if (! in_array($invoice->status, [InvoiceStatus::Issued, InvoiceStatus::Sent], true)) {
            throw ValidationException::withMessages(['invoice' => 'Only issued invoices can be marked as sent.']);
        }

        $invoice->forceFill(['status' => InvoiceStatus::Sent])->save();
        $this->timeline->execute($invoice, 'invoice.sent', 'Invoice marked as sent', $user);
        activity('finance')->causedBy($user)->performedOn($invoice)->log('Invoice marked as sent');

        return $invoice->refresh();
    }
}
