<?php

namespace App\Modules\Invoices\Events;

use App\Modules\Invoices\Models\Invoice;

class InvoiceIssued
{
    public function __construct(public Invoice $invoice) {}
}
