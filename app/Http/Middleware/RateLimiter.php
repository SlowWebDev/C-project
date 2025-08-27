<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter as CacheRateLimiter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RateLimiter
{
    protected $limiter;

    public function __construct(CacheRateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $key = $request->ip();
        
        if ($this->limiter->tooManyAttempts($key, 60)) { // 60  per minute
            return response()->json([
                'message' => 'Too many requests. Please try again later.',
                'retry_after' => $this->limiter->availableIn($key)
            ], 429);
        }

        $this->limiter->hit($key, 60);

        return $next($request);
    }
}
