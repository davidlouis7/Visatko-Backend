<?php

namespace App\Modules\CRM\Actions;

use App\Models\User;
use App\Modules\CRM\Models\Timeline;
use Illuminate\Database\Eloquent\Model;

class AddTimelineEntry
{
    public function execute(Model $subject, string $type, string $title, ?User $user = null, ?string $description = null, ?array $oldValue = null, ?array $newValue = null): Timeline
    {
        return $subject->timelines()->create(['user_id' => $user?->id, 'type' => $type, 'title' => $title, 'description' => $description, 'old_value' => $oldValue, 'new_value' => $newValue, 'created_at' => now()]);
    }
}
