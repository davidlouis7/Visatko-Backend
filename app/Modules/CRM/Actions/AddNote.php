<?php

namespace App\Modules\CRM\Actions;

use App\Models\User;
use App\Modules\CRM\Models\Note;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AddNote
{
    public function __construct(private AddTimelineEntry $timeline) {}

    public function execute(Model $subject, User $user, string $note, bool $private = true): Note
    {
        return DB::transaction(function () use ($subject, $user, $note, $private): Note {
            $record = $subject->notes()->create(['user_id' => $user->id, 'note' => $note, 'is_private' => $private]);
            $this->timeline->execute($subject, 'note_added', 'Internal note added', $user, $note);
            activity('crm')->causedBy($user)->performedOn($subject)->log('Internal note added');

            return $record;
        });
    }
}
