<?php

namespace App\Modules\ContactMessages\Models;

use App\Models\User;
use App\Modules\ContactMessages\Enums\ContactMessageStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactMessage extends Model
{
    use SoftDeletes;

    protected $fillable = ['full_name', 'email', 'phone', 'subject', 'message', 'status', 'assigned_to', 'source', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term', 'landing_page', 'referrer', 'gclid', 'fbclid', 'meta_event_id', 'ip_address', 'user_agent'];

    protected function casts(): array
    {
        return ['status' => ContactMessageStatus::class];
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
