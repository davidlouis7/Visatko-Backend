<?php

namespace App\Modules\Pages\Models;

use App\Support\Translations\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use HasTranslations, SoftDeletes;

    public const TRANSLATION_MODEL = PageTranslation::class;

    protected $fillable = ['key', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
