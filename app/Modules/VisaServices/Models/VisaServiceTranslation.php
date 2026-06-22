<?php

namespace App\Modules\VisaServices\Models;

use App\Modules\Languages\Models\Language;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisaServiceTranslation extends Model
{
    protected $fillable = ['language_id', 'title', 'slug', 'short_description', 'full_description', 'requirements', 'required_documents', 'terms_conditions', 'meta_title', 'meta_description', 'meta_keywords'];

    public function visaService(): BelongsTo
    {
        return $this->belongsTo(VisaService::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
