<?php

use App\Http\Controllers\LocaleController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'appName' => config('app.name'),
        'locale' => app()->getLocale(),
    ]);
})->name('home');

Route::post('lang/switch', [LocaleController::class, 'switch'])->name('lang.switch');
