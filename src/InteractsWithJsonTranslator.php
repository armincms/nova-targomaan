<?php

namespace Armincms\Fields;

trait InteractsWithJsonTranslator
{
    /**
     * Get the searchable columns for the resource.
     *
     * @return array
     */
    public static function searchableColumns()
    {
        return collect(static::$searchJson)->flatMap(function ($column) {
            return collect(static::locales($column))->map(function ($language, $locale) use ($column) {
                return "{$column}->{$locale}";
            });
        })->merge(parent::searchableColumns())->all();
    }

    /**
     * Get the searchable locales.
     *
     * @return array
     */
    public static function locales($column): array
    {
        return Targomaan::locales($column);
    }
}
