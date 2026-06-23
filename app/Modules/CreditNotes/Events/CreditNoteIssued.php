<?php

namespace App\Modules\CreditNotes\Events;

use App\Modules\CreditNotes\Models\CreditNote;

class CreditNoteIssued
{
    public function __construct(public CreditNote $creditNote) {}
}
