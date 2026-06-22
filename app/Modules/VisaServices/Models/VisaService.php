<?php

namespace App\Modules\VisaServices\Models;

use App\Modules\Countries\Models\Country;
use App\Modules\Media\Models\Media;
use App\Support\Translations\HasTranslations;
use Database\Factories\VisaServiceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class VisaService extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    public const TRANSLATION_MODEL = VisaServiceTranslation::class;

    protected $fillable = ['country_id', 'thumbnail_media_id', 'banner_media_id', 'price', 'discount_price', 'currency', 'processing_time', 'visa_validity', 'stay_duration', 'is_featured', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return ['price' => 'decimal:2', 'discount_price' => 'decimal:2', 'is_featured' => 'boolean', 'is_active' => 'boolean', 'sort_order' => 'integer'];
    }

    protected static function newFactory(): VisaServiceFactory
    {
        return VisaServiceFactory::new();
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function thumbnail(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'thumbnail_media_id');
    }

    public function banner(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'banner_media_id');
    }

    public function gallery(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'visa_service_media')->withPivot('sort_order')->orderByPivot('sort_order');
    }
}
