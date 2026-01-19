<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TestingToken
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = config('app.testing_token');

        if (! $token || $request->header('X-Testing-Token') !== $token) {
            abort(403, 'Invalid testing token');
        }

        return $next($request);
    }
}
