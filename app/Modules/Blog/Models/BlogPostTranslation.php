<?php

namespace App\Modules\Blog\Models;

use App\Modules\Languages\Models\Language;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogPostTranslation extends Model
{
    protected $fillable = ['language_id', 'title', 'slug', 'excerpt', 'content', 'meta_title', 'meta_description', 'meta_keywords'];

    public function blogPost(): BelongsTo
    {
        return $this->belongsTo(BlogPost::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
