<?php

namespace App\Modules\Reviews\Models;

use App\Modules\Media\Models\Media;
use App\Modules\VisaServices\Models\VisaService;
use App\Support\Cache\ClearsPublicApiCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use ClearsPublicApiCache, SoftDeletes;

    protected $fillable = ['customer_name', 'customer_country', 'visa_service_id', 'rating', 'review_text', 'customer_image_media_id', 'is_active', 'is_featured', 'sort_order'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'is_featured' => 'boolean'];
    }

    public function visaService(): BelongsTo
    {
        return $this->belongsTo(VisaService::class);
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'customer_image_media_id');
    }
}
