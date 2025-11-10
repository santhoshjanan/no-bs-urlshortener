<?php

declare(strict_types=1);

use App\Http\Controllers\UrlController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/shorten', [UrlController::class, 'api_shortener'])
    ->middleware('throttle:10,1');
Route::get('/{shortened}', [UrlController::class, 'redirect']);

Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now()->toISOString(),
        'version' => config('app.version'),
    ]);
});
