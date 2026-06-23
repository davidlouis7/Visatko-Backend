<?php

namespace App\Modules\Invoices\Actions;

use App\Modules\Finance\Services\FinanceSettings;
use App\Modules\Invoices\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

class GenerateInvoicePdfAction
{
    public function __construct(private readonly FinanceSettings $settings) {}

    public function execute(Invoice $invoice): \Barryvdh\DomPDF\PDF
    {
        return Pdf::loadView('pdf.invoice', [
            'invoice' => $invoice->load(['customer', 'items']),
            'finance' => $this->settings,
        ])->setPaper('a4');
    }
}
