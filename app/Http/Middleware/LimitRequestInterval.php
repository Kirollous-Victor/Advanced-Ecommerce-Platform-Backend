<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class LimitRequestInterval
{
    public function handle(Request $request, Closure $next, int $maxAttempts = 2, int $decaySeconds = 10): JsonResponse
    {
        if (app()->environment('local')) {
            $key = $request->route()->getName() . ':' . $request->ip();
            $attempt = RateLimiter::attempt($key, $maxAttempts, function () {
            }, $decaySeconds);
            if (!$attempt)
                return response()->json(['error' => 'Too many requests. Please try again later',
                    'available_in' => RateLimiter::availableIn($key)], 429);
            $response = $next($request);
            $data = $response->getData(true);
            if (RateLimiter::remaining($key, $maxAttempts))
                $data['attempts_left'] = RateLimiter::remaining($key, $maxAttempts);
            else
                $data['available_in'] = RateLimiter::availableIn($key);
            $response->setData($data);
            return $response;
        }
        return $next($request);
    }
}
