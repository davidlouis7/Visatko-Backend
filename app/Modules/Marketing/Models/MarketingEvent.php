<?php

namespace App\Modules\Marketing\Models;

use App\Modules\Customers\Models\Customer;
use App\Modules\Marketing\Enums\MarketingEventStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MarketingEvent extends Model
{
    protected $fillable = ['event_name', 'event_id', 'status', 'related_type', 'related_id', 'customer_id', 'email_hash', 'phone_hash', 'ip_address', 'user_agent', 'fbc', 'fbp', 'source_url', 'payload', 'response', 'error_message', 'sent_at'];

    protected function casts(): array
    {
        return ['status' => MarketingEventStatus::class, 'payload' => 'array', 'response' => 'array', 'sent_at' => 'datetime'];
    }

    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
