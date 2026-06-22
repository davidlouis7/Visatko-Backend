<?php

namespace App\Modules\Countries\Models;

use App\Modules\Languages\Models\Language;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CountryTranslation extends Model
{
    protected $fillable = ['language_id', 'name', 'slug', 'description', 'meta_title', 'meta_description', 'meta_keywords'];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
