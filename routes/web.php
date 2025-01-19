<?php

use App\Http\Controllers\UrlController;
use Illuminate\Support\Facades\Route;

Route::get('/', [UrlController::class, 'index'])->name('index');
Route::post('/', [UrlController::class, 'shorten'])->name('shorten');

//Route::post('/shorten', [UrlController::class, 'shorten'])->name('shorten');
//Route::get('/shorten', function () {
//    return redirect()->route('index')->with('message', 'Please enter a URL to shorten.');
//});
Route::get('/{shortened}', [UrlController::class, 'redirect']);
