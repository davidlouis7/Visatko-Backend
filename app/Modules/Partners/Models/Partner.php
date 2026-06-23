<?php

namespace App\Modules\Partners\Models;

use App\Modules\Media\Models\Media;
use App\Support\Cache\ClearsPublicApiCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Partner extends Model
{
    use ClearsPublicApiCache, SoftDeletes;

    protected $fillable = ['company_name', 'logo_media_id', 'website_url', 'description', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function logo(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'logo_media_id');
    }
}
