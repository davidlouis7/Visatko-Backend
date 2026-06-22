<?php

namespace App\Support\Translations;

use App\Modules\Languages\Models\Language;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class SyncTranslations
{
    public function execute(Model $model, array $translations, array $fields): void
    {
        $languages = Language::query()->whereIn('code', array_keys($translations))->get()->keyBy('code');
        $unknown = array_values(array_diff(array_keys($translations), $languages->keys()->all()));

        if ($unknown !== []) {
            throw ValidationException::withMessages([
                'translations' => ['Unknown language codes: '.implode(', ', $unknown)],
            ]);
        }

        $defaultCode = Language::query()->where('is_default', true)->value('code');
        if ($model->wasRecentlyCreated && $defaultCode && ! array_key_exists($defaultCode, $translations)) {
            throw ValidationException::withMessages([
                'translations' => ["A {$defaultCode} translation is required."],
            ]);
        }

        foreach ($translations as $locale => $values) {
            if (isset($values['slug'])) {
                $relation = $model->translations();
                $duplicate = $relation->getRelated()->newQuery()
                    ->where('language_id', $languages[$locale]->id)
                    ->where('slug', $values['slug'])
                    ->where($relation->getForeignKeyName(), '!=', $model->getKey())
                    ->exists();

                if ($duplicate) {
                    throw ValidationException::withMessages([
                        "translations.{$locale}.slug" => ['The slug has already been taken for this language.'],
                    ]);
                }
            }

            $model->translations()->updateOrCreate(
                ['language_id' => $languages[$locale]->id],
                array_intersect_key($values, array_flip($fields)),
            );
        }
    }
}
