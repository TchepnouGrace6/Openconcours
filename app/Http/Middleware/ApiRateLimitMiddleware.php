<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class ApiRateLimitMiddleware
{
    public function handle(Request $request, Closure $next, $maxAttempts = 60, $decayMinutes = 1)
    {
        $key = $this->resolveRequestSignature($request);

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);

            Log::warning('Rate limit dépassé', [
                'ip' => $request->ip(),
                'user_id' => auth()->id(),
                'route' => $request->route()->getName(),
                'attempts' => RateLimiter::attempts($key),
                'max_attempts' => $maxAttempts,
            ]);

            return response()->json([
                'message' => 'Trop de requêtes. Veuillez patienter.',
                'error' => 'RATE_LIMIT_EXCEEDED',
                'retry_after' => $seconds,
            ], 429)->header('Retry-After', $seconds);
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        $response = $next($request);

        return $response->header('X-RateLimit-Limit', $maxAttempts)
            ->header('X-RateLimit-Remaining', $maxAttempts - RateLimiter::attempts($key));
    }

    protected function resolveRequestSignature(Request $request)
    {
        if ($user = $request->user()) {
            return sha1('user:'.$user->id.'|route:'.$request->route()->getName());
        }

        return sha1('ip:'.$request->ip().'|route:'.$request->route()->getName());
    }
}
