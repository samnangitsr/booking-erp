<?php

namespace App\Http\Controllers;

use App\Support\Translations;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    /** @var array<int, string> */
    private const SUPPORTED = ['en', 'km'];

    public function switch(Request $request): JsonResponse
    {
        $locale = (string) $request->input('locale', 'en');
        if (! in_array($locale, self::SUPPORTED, true)) {
            $locale = config('app.fallback_locale', 'en');
        }

        session(['locale' => $locale]);
        app()->setLocale($locale);

        return response()->json([
            'locale' => $locale,
            'translations' => Translations::load($locale),
        ]);
    }
}
