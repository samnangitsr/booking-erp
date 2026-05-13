<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocaleController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('api/homepage', [HomeController::class, 'data'])->name('home.data');

Route::post('lang/switch', [LocaleController::class, 'switch'])->name('lang.switch');
