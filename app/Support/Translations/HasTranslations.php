<?php

namespace App\Support\Translations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasTranslations
{
    public function translations(): HasMany
    {
        return $this->hasMany(static::TRANSLATION_MODEL);
    }

    public function translationFor(?string $locale = null): ?Model
    {
        $locale ??= request()->attributes->get('locale', app()->getLocale());
        $translations = $this->relationLoaded('translations')
            ? $this->translations
            : $this->translations()->with('language')->get();

        return $translations->first(fn (Model $translation): bool => $translation->language?->code === $locale)
            ?? $translations->first(fn (Model $translation): bool => $translation->language?->is_default)
            ?? $translations->first();
    }
}
