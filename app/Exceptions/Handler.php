<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

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
        $this->renderable(function (Throwable $e) {
            if (request()->expectsJson() || request()->is('api/*')) {
                if ($e instanceof ValidationException) {
                    return response()->json([
                        'message' => 'Validation error',
                        'errors' => $e->errors(),
                    ], 422);
                }

                if ($e instanceof AuthenticationException) {
                    return response()->json([
                        'message' => 'Unauthenticated',
                    ], 401);
                }

                if ($e instanceof NotFoundHttpException) {
                    return response()->json([
                        'message' => 'Not found',
                    ], 404);
                }

                if ($e instanceof MethodNotAllowedHttpException) {
                    return response()->json([
                        'message' => 'Method not allowed',
                    ], 405);
                }

                // Handle any other exceptions
                return response()->json([
                    'message' => 'Server error',
                    'error' => $e->getMessage()
                ], 500);
            }
        });
    }
} 