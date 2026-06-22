<?php

namespace App\Modules\Blog\Models;

use App\Modules\Languages\Models\Language;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogTagTranslation extends Model
{
    protected $fillable = ['language_id', 'name', 'slug'];

    public function blogTag(): BelongsTo
    {
        return $this->belongsTo(BlogTag::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
