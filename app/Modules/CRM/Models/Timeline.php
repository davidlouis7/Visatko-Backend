<?php

namespace App\Modules\CRM\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Timeline extends Model
{
    public $timestamps = false;

    protected $fillable = ['subject_type', 'subject_id', 'user_id', 'type', 'title', 'description', 'old_value', 'new_value', 'created_at'];

    protected function casts(): array
    {
        return ['old_value' => 'array', 'new_value' => 'array', 'created_at' => 'datetime'];
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
