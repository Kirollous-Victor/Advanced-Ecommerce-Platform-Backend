<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class LimitRequestInterval
{
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->environment('production')) {
            $ex = RateLimiter::attempt($request->route()->getName() . ':' . $request->ip(), 1,
                function () {
                }, 8);
            if (!$ex)
                return response()->json(['error' => 'Too many requests. Please try again after ' .
                    RateLimiter::availableIn($request->route()->getName() . ':' . $request->ip()) .
                    ' seconds'], 429);
        }
        return $next($request);
    }
}
