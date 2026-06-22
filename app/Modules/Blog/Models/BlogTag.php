<?php

namespace App\Modules\Blog\Models;

use App\Support\Translations\HasTranslations;
use Database\Factories\BlogTagFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogTag extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    public const TRANSLATION_MODEL = BlogTagTranslation::class;

    protected $fillable = ['is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): BlogTagFactory
    {
        return BlogTagFactory::new();
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(BlogPost::class, 'blog_post_tag');
    }
}
