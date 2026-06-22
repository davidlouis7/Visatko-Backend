<?php

namespace App\Modules\Countries\Models;

use App\Modules\Media\Models\Media;
use App\Modules\VisaServices\Models\VisaService;
use App\Support\Translations\HasTranslations;
use Database\Factories\CountryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    public const TRANSLATION_MODEL = CountryTranslation::class;

    protected $fillable = ['code', 'flag_media_id', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'sort_order' => 'integer'];
    }

    protected static function newFactory(): CountryFactory
    {
        return CountryFactory::new();
    }

    public function flag(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'flag_media_id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(VisaService::class);
    }
}
