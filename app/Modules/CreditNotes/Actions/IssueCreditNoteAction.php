<?php

namespace App\Modules\CreditNotes\Actions;

use App\Models\User;
use App\Modules\CreditNotes\Enums\CreditNoteStatus;
use App\Modules\CreditNotes\Events\CreditNoteIssued;
use App\Modules\CreditNotes\Models\CreditNote;
use Illuminate\Validation\ValidationException;

class IssueCreditNoteAction
{
    public function execute(CreditNote $creditNote, ?User $user = null): CreditNote
    {
        if ($creditNote->status !== CreditNoteStatus::Draft) {
            throw ValidationException::withMessages(['credit_note' => 'Only draft credit notes can be issued.']);
        }

        $creditNote->forceFill(['status' => CreditNoteStatus::Issued, 'issued_at' => now()])->save();
        activity('finance')->causedBy($user)->performedOn($creditNote)->log('Credit note issued');
        event(new CreditNoteIssued($creditNote));

        return $creditNote->refresh()->load(['invoice', 'items']);
    }
}
