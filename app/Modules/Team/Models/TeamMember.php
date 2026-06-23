<?php

namespace App\Modules\Team\Models;

use App\Modules\Media\Models\Media;
use App\Support\Cache\ClearsPublicApiCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeamMember extends Model
{
    use ClearsPublicApiCache, SoftDeletes;

    protected $fillable = ['name', 'job_title', 'image_media_id', 'bio', 'email', 'phone', 'social_links', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return ['social_links' => 'array', 'is_active' => 'boolean'];
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'image_media_id');
    }
}
