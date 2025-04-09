<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;

class LanguageService
{


    public static function getMultiLanguage()
    {
        // Get_Multi_Language
        // return request()->header('Get-Multi-Language', false);
        return true;
    }


    public static function translatableFieldRules(string $baseRule): array
    {
        $locales = config('translatable.locales');
        $defaultLocale = config('translatable.default_locale');
        $rules = ['required', 'array'];

        $rules[] = function ($attribute, $value, $fail) use ($locales, $defaultLocale, $baseRule) {

            $attributeName = trans("attributes.{$attribute}");

            if (!is_array($value)) {
                $fail(trans('validation.translatable.required_array', [
                    'attribute' => $attributeName,
                ]));
                return;
            }

            foreach ($locales as $locale) {
                $localizedValue = $value[$locale] ?? null;

                if (strpos($baseRule, 'required') !== false && $locale === $defaultLocale && ($localizedValue === null || $localizedValue === '')) {
                    $fail(trans('validation.translatable.required_locale', [
                        'attribute' => $attributeName,
                        'locale' => $locale,
                    ]));
                }

                if ($localizedValue !== null) {
                    $validator = validator([$locale => $localizedValue], [$locale => $baseRule]);
                    if ($validator->fails()) {
                        $fail(trans('validation.translatable.invalid_locale', [
                            'attribute' => $attributeName,
                            'locale' => $locale,
                            'error' => $validator->errors()->first($locale),
                        ]));
                    }
                }
            }
        };

        return $rules;
    }




    public static function prepareTranslatableData(array $data, $existingEntity = null): array
    {
        $translatableFields = (new $existingEntity)->getTranslatableAttributes();
        $locales = config('translatable.locales');

        foreach ($translatableFields as $field) {
            $currentTranslations = $existingEntity ? $existingEntity->getTranslations($field) : [];

            if (isset($data[$field]) && is_array($data[$field])) {
                foreach ($locales as $locale) {
                    if (array_key_exists($locale, $data[$field])) {
                        $newValue = $data[$field][$locale];


                        if ($newValue === null || $newValue === "" || trim($newValue) === "") {
                            $currentTranslations[$locale] = "";
                            continue;
                        }

                        $currentTranslations[$locale] = $newValue;
                    }
                }
            }

            $data[$field] = $currentTranslations;
        }

        return $data;
    }
}
