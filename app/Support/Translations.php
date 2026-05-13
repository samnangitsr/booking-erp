<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

class Translations
{
    /**
     * Build the translation dictionary for the given locale. Returns a flat
     * map of dotted keys → translated strings, e.g. `admin.nav.dashboard => "Dashboard"`.
     *
     * Only files inside resources/lang/{locale}/admin*.php and {locale}/messages.php
     * are exported to the frontend by default to keep the payload small.
     */
    public static function load(string $locale): array
    {
        $base = lang_path($locale);
        if (! is_dir($base)) {
            $base = base_path('lang/'.$locale);
        }
        if (! is_dir($base)) {
            return [];
        }

        $output = [];
        foreach (File::files($base) as $file) {
            $namespace = $file->getBasename('.'.$file->getExtension());
            $contents = require $file->getPathname();
            if (is_array($contents)) {
                self::flatten($contents, $namespace, $output);
            }
        }

        return $output;
    }

    /**
     * Returns the translations dictionary for the current request's locale.
     */
    public static function current(): array
    {
        return self::load(app()->getLocale());
    }

    private static function flatten(array $items, string $prefix, array &$output): void
    {
        foreach ($items as $key => $value) {
            $fullKey = $prefix === '' ? (string) $key : $prefix.'.'.$key;
            if (is_array($value)) {
                self::flatten($value, $fullKey, $output);
            } else {
                $output[$fullKey] = (string) $value;
            }
        }
    }
}
