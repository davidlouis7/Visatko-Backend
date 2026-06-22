<?php

namespace App\Modules\Blog\Models;

use App\Modules\Languages\Models\Language;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogCategoryTranslation extends Model
{
    protected $fillable = ['language_id', 'name', 'slug', 'description', 'meta_title', 'meta_description', 'meta_keywords'];

    public function blogCategory(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
