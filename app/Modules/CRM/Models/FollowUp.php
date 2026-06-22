<?php

namespace App\Modules\CRM\Models;

use App\Models\User;
use App\Modules\CRM\Enums\FollowUpStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class FollowUp extends Model
{
    protected $fillable = ['subject_type', 'subject_id', 'assigned_to', 'created_by', 'due_at', 'status', 'title', 'notes', 'completed_at'];

    protected function casts(): array
    {
        return ['status' => FollowUpStatus::class, 'due_at' => 'datetime', 'completed_at' => 'datetime'];
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
