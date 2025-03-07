<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function unauthenticated($request, array $guards)
    {
        if ($request->is('api/*') || $request->expectsJson()) {
            abort(response()->json([
                'message' => 'Unauthenticated.',
                'status' => 401
            ], 401));
        }
    }
} 