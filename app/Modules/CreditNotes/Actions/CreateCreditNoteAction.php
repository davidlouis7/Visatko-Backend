<?php

namespace App\Modules\CreditNotes\Actions;

use App\Models\User;
use App\Modules\CreditNotes\Enums\CreditNoteStatus;
use App\Modules\CreditNotes\Models\CreditNote;
use App\Modules\Finance\Services\FinanceSettings;
use App\Modules\Invoices\Enums\InvoiceStatus;
use App\Modules\Invoices\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateCreditNoteAction
{
    public function __construct(private readonly FinanceSettings $settings) {}

    /** @param array<string, mixed> $data */
    public function execute(array $data, ?User $user = null): CreditNote
    {
        return DB::transaction(function () use ($data, $user): CreditNote {
            $invoice = Invoice::query()->findOrFail($data['invoice_id']);
            if (! in_array($invoice->status, [InvoiceStatus::Issued, InvoiceStatus::Sent, InvoiceStatus::Paid, InvoiceStatus::PartiallyPaid], true)) {
                throw ValidationException::withMessages(['invoice_id' => 'Credit notes can only be created against issued invoices.']);
            }

            $creditNote = CreditNote::query()->create([
                'credit_note_number' => sprintf('%s-%s-%05d', $this->settings->get('credit_note_prefix', 'CN'), now()->format('Y'), CreditNote::withTrashed()->count() + 1),
                'invoice_id' => $invoice->id,
                'customer_id' => $invoice->customer_id,
                'created_by' => $user?->id,
                'status' => CreditNoteStatus::Draft,
                'reason' => $data['reason'] ?? null,
            ]);

            $subtotal = 0.0;
            $vat = 0.0;
            foreach ($data['items'] as $item) {
                $lineSubtotal = round((float) $item['quantity'] * (float) $item['unit_price'], 2);
                $rate = (float) ($item['vat_rate'] ?? $invoice->vat_rate);
                $lineVat = round($lineSubtotal * ($rate / 100), 2);
                $creditNote->items()->create([
                    'invoice_item_id' => $item['invoice_item_id'] ?? null,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'vat_rate' => $rate,
                    'vat_amount' => $lineVat,
                    'line_total' => round($lineSubtotal + $lineVat, 2),
                ]);
                $subtotal += $lineSubtotal;
                $vat += $lineVat;
            }

            $creditNote->forceFill(['subtotal' => $subtotal, 'vat_amount' => $vat, 'total' => round($subtotal + $vat, 2)])->save();
            activity('finance')->causedBy($user)->performedOn($creditNote)->log('Credit note created');

            return $creditNote->load(['invoice', 'items']);
        });
    }
}
