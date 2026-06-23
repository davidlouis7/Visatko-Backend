<?php

namespace App\Modules\CreditNotes\Actions;

use App\Modules\CreditNotes\Models\CreditNote;
use Barryvdh\DomPDF\Facade\Pdf;

class GenerateCreditNotePdfAction
{
    public function execute(CreditNote $creditNote): \Barryvdh\DomPDF\PDF
    {
        return Pdf::loadView('pdf.credit-note', ['creditNote' => $creditNote->load(['invoice.customer', 'items'])])->setPaper('a4');
    }
}
