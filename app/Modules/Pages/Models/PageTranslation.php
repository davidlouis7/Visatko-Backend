<?php

namespace App\Modules\Pages\Models;

use App\Modules\Languages\Models\Language;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageTranslation extends Model
{
    protected $fillable = ['language_id', 'title', 'slug', 'content', 'meta_title', 'meta_description', 'meta_keywords'];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
