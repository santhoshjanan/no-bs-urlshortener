<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof UrlNotFoundException) {
            return response()->json(['error' => 'URL not found'], 404);
        }

        if ($exception instanceof InvalidUrlException) {
            return response()->json(['error' => 'Invalid redirect target'], 400);
        }

        return parent::render($request, $exception);
    }
}
