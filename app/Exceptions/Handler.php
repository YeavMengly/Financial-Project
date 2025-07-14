<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Support\Facades\Log;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e) {
        $e = $this->prepareException($e);
        $status_code = (int) method_exists($e, 'getStatusCode') ? $e->getStatusCode() : $e->getCode();
        if ($status_code === 404) {
            Log::error('Page not found: ' . $request->url());
        }

        return parent::render($request, $e);
    }
}
