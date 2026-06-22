<?php

namespace App\Modules\Blog\Models;

use App\Support\Translations\HasTranslations;
use Database\Factories\BlogCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogCategory extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    public const TRANSLATION_MODEL = BlogCategoryTranslation::class;

    protected $fillable = ['is_active', 'sort_order'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'sort_order' => 'integer'];
    }

    protected static function newFactory(): BlogCategoryFactory
    {
        return BlogCategoryFactory::new();
    }

    public function posts(): HasMany
    {
        return $this->hasMany(BlogPost::class, 'category_id');
    }
}
