<?php

namespace App\Modules\ApplicationDocuments\Policies;

use App\Models\User;
use App\Modules\ApplicationDocuments\Models\ApplicationDocument;

class ApplicationDocumentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('documents.view');
    }

    public function view(User $user, ApplicationDocument $document): bool
    {
        return $user->can('documents.view');
    }

    public function create(User $user): bool
    {
        return $user->can('documents.upload');
    }

    public function update(User $user, ApplicationDocument $document): bool
    {
        return $user->can('documents.review');
    }

    public function delete(User $user, ApplicationDocument $document): bool
    {
        return $user->can('documents.delete');
    }
}
