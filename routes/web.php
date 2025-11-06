<?php

use App\Http\Controllers\UrlController;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [UrlController::class, 'index'])->name('index');
Route::post('/', [UrlController::class, 'web_shortener'])
    ->middleware('throttle:10,1')
    ->name('shorten');

Route::get('/{shortened}', [UrlController::class, 'redirect']);
