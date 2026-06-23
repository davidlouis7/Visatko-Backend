<?php

namespace App\Modules\Invoices\Actions;

use App\Models\User;
use App\Modules\CRM\Actions\AddTimelineEntry;
use App\Modules\Finance\Services\FinanceSettings;
use App\Modules\Invoices\Enums\InvoicePaymentStatus;
use App\Modules\Invoices\Enums\InvoiceStatus;
use App\Modules\Invoices\Models\Invoice;
use Illuminate\Support\Facades\DB;

class CreateInvoiceAction
{
    public function __construct(
        private readonly CalculateInvoiceTotalsAction $totals,
        private readonly FinanceSettings $settings,
        private readonly AddTimelineEntry $timeline,
    ) {}

    /** @param array<string, mixed> $data */
    public function execute(array $data, ?User $user = null): Invoice
    {
        return DB::transaction(function () use ($data, $user): Invoice {
            $totals = $this->totals->execute($data['items']);
            $invoice = Invoice::query()->create([
                'invoice_number' => $this->nextNumber($this->settings->get('invoice_prefix', 'INV')),
                'customer_id' => $data['customer_id'],
                'visa_application_id' => $data['visa_application_id'] ?? null,
                'created_by' => $user?->id,
                'status' => InvoiceStatus::Draft,
                'payment_status' => InvoicePaymentStatus::Unpaid,
                'currency' => $data['currency'] ?? 'AED',
                'subtotal' => $totals['subtotal'],
                'discount_total' => $totals['discount_total'],
                'taxable_amount' => $totals['taxable_amount'],
                'vat_rate' => $totals['vat_rate'],
                'vat_amount' => $totals['vat_amount'],
                'total' => $totals['total'],
                'amount_paid' => 0,
                'amount_due' => $totals['total'],
                'notes' => $data['notes'] ?? null,
                'terms' => $data['terms'] ?? null,
                'meta' => $data['meta'] ?? null,
            ]);

            foreach ($totals['items'] as $line) {
                $invoice->items()->create($line);
            }

            $this->timeline->execute($invoice, 'invoice.created', 'Invoice created', $user);
            activity('finance')->causedBy($user)->performedOn($invoice)->log('Invoice created');

            return $invoice->load(['customer', 'visaApplication', 'items']);
        });
    }

    private function nextNumber(string $prefix): string
    {
        return sprintf('%s-%s-%05d', $prefix, now()->format('Y'), Invoice::withTrashed()->count() + 1);
    }
}
