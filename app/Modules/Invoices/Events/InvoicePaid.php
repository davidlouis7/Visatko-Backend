<?php

namespace App\Modules\Invoices\Events;

use App\Modules\Invoices\Models\Invoice;

class InvoicePaid
{
    public function __construct(public Invoice $invoice) {}
}
