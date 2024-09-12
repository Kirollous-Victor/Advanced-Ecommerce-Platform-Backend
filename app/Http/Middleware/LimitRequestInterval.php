<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class LimitRequestInterval
{
    public function handle(Request $request, Closure $next, int $attempts = 2, int $decay = 8): JsonResponse
    {
        if (app()->environment('production')) {
            $key = $request->route()->getName() . ':' . $request->ip();
            $ex = RateLimiter::attempt($key, $attempts, function () use ($request, $next) {
                return $next($request);
            }, $decay);
            if (!$ex)
                return response()->json(['error' => 'Too many requests. Please try again after ' .
                    RateLimiter::availableIn($key) . ' seconds'], 429);
        }
        return $next($request);
    }
}
