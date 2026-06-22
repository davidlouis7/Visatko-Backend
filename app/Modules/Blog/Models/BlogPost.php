<?php

namespace App\Modules\Blog\Models;

use App\Models\User;
use App\Modules\Media\Models\Media;
use App\Support\Translations\HasTranslations;
use Database\Factories\BlogPostFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogPost extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    public const TRANSLATION_MODEL = BlogPostTranslation::class;

    protected $fillable = ['author_id', 'category_id', 'thumbnail_media_id', 'banner_media_id', 'is_published', 'is_featured', 'published_at', 'sort_order'];

    protected function casts(): array
    {
        return ['is_published' => 'boolean', 'is_featured' => 'boolean', 'published_at' => 'datetime', 'sort_order' => 'integer'];
    }

    protected static function newFactory(): BlogPostFactory
    {
        return BlogPostFactory::new();
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    public function thumbnail(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'thumbnail_media_id');
    }

    public function banner(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'banner_media_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(BlogTag::class, 'blog_post_tag');
    }
}
