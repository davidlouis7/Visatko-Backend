<?php

namespace App\Modules\CreditNotes\Policies;

use App\Models\User;
use App\Modules\CreditNotes\Models\CreditNote;

class CreditNotePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('credit_notes.view');
    }

    public function view(User $user, CreditNote $creditNote): bool
    {
        return $user->can('credit_notes.view');
    }

    public function create(User $user): bool
    {
        return $user->can('credit_notes.create');
    }

    public function issue(User $user, CreditNote $creditNote): bool
    {
        return $user->can('credit_notes.issue');
    }

    public function download(User $user, CreditNote $creditNote): bool
    {
        return $user->can('credit_notes.download');
    }
}
