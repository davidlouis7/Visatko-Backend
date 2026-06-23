<?php

namespace App\Modules\Invoices\Policies;

use App\Models\User;
use App\Modules\Invoices\Models\Invoice;

class InvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('invoices.view');
    }

    public function view(User $user, Invoice $invoice): bool
    {
        return $user->can('invoices.view');
    }

    public function create(User $user): bool
    {
        return $user->can('invoices.create');
    }

    public function update(User $user, Invoice $invoice): bool
    {
        return $user->can('invoices.update');
    }

    public function issue(User $user, Invoice $invoice): bool
    {
        return $user->can('invoices.issue');
    }

    public function send(User $user, Invoice $invoice): bool
    {
        return $user->can('invoices.send');
    }

    public function download(User $user, Invoice $invoice): bool
    {
        return $user->can('invoices.download');
    }
}
