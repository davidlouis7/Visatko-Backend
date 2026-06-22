<?php

namespace App\Modules\Media\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Media extends Model
{
    use HasUuids;

    protected $table = 'media';

    protected $fillable = [
        'public_id', 'disk', 'path', 'original_name', 'mime_type', 'size',
        'visibility', 'collection', 'mediable_type', 'mediable_id', 'uploaded_by', 'metadata',
    ];

    protected function casts(): array
    {
        return ['metadata' => 'array', 'size' => 'integer'];
    }

    public function uniqueIds(): array
    {
        return ['public_id'];
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
