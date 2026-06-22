<?php

namespace App\Modules\CRM\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Note extends Model
{
    protected $fillable = ['subject_type', 'subject_id', 'user_id', 'note', 'is_private'];

    protected function casts(): array
    {
        return ['is_private' => 'boolean'];
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
