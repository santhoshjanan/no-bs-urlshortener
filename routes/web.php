<?php

use App\Http\Controllers\UrlController;
use Illuminate\Support\Facades\Route;

Route::get('/', [UrlController::class, 'index'])->name('index');
Route::post('/', [UrlController::class, 'web_shortener'])
    ->middleware('throttle:10,1')
    ->name('shorten');

// Static pages
Route::view('/about', 'about')->name('about');
Route::view('/faq', 'faq')->name('faq');
Route::view('/privacy', 'privacy')->name('privacy');
Route::view('/terms', 'terms')->name('terms');

Route::get('/{shortened}', [UrlController::class, 'redirect']);
