<?php

namespace App\Modules\Emails\Models;

use App\Modules\Emails\Enums\EmailLogStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class EmailLog extends Model
{
    protected $fillable = ['template_key', 'subject', 'recipient_email', 'recipient_name', 'status', 'related_type', 'related_id', 'payload', 'error_message', 'sent_at', 'failed_at'];

    protected function casts(): array
    {
        return ['status' => EmailLogStatus::class, 'payload' => 'array', 'sent_at' => 'datetime', 'failed_at' => 'datetime'];
    }

    public function related(): MorphTo
    {
        return $this->morphTo();
    }
}
