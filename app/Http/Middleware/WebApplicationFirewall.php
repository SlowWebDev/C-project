<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class WebApplicationFirewall
{
    /**
     * Suspicious patterns that indicate potential attacks
     */
    private array $suspiciousPatterns = [
        // SQL Injection patterns
        '/union.*select/i',
        '/\bor\b.*\b1\s*=\s*1/i',
        '/drop\s+table/i',
        '/insert\s+into/i',
        '/delete\s+from/i',
        '/update.*set/i',
        
        // XSS patterns
        '/<script[^>]*>.*?<\/script>/i',
        '/javascript\s*:/i',
        '/on\w+\s*=/i',
        '/<iframe[^>]*>.*?<\/iframe>/i',
        
        // Command injection
        '/;\s*(cat|ls|pwd|whoami|id|uname)/i',
        '/\|\s*(cat|ls|pwd|whoami|id|uname)/i',
        '/&&\s*(cat|ls|pwd|whoami|id|uname)/i',
        
        // Path traversal
        '/\.\.\//',
        '/\.\.\\\\/',
        '/etc\/passwd/i',
        '/boot\.ini/i',
        
        // Other suspicious patterns
        '/<\?php/i',
        '/eval\s*\(/i',
        '/base64_decode/i',
    ];

    /**
     * Blocked User Agents
     */
    private array $blockedUserAgents = [
        'sqlmap',
        'nikto',
        'nmap',
        'masscan',
        'w3af',
        'acunetix',
        'havij',
        'pangolin',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check user agent
        if ($this->isBlockedUserAgent($request)) {
            $this->logSuspiciousActivity($request, 'Blocked User Agent');
            abort(403, 'Forbidden');
        }

        // Check for suspicious patterns in all input
        if ($this->containsSuspiciousContent($request)) {
            $this->logSuspiciousActivity($request, 'Suspicious Content Detected');
            abort(403, 'Forbidden');
        }

        // Rate limiting by IP (basic implementation)
        if ($this->isRateLimited($request)) {
            $this->logSuspiciousActivity($request, 'Rate Limit Exceeded');
            abort(429, 'Too Many Requests');
        }

        return $next($request);
    }

    /**
     * Check if user agent is blocked
     */
    private function isBlockedUserAgent(Request $request): bool
    {
        $userAgent = strtolower($request->userAgent() ?? '');
        
        foreach ($this->blockedUserAgents as $blocked) {
            if (str_contains($userAgent, $blocked)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check for suspicious content in request
     */
    private function containsSuspiciousContent(Request $request): bool
    {
        // Check all input data
        $allInput = array_merge(
            $request->all(),
            [$request->getPathInfo(), $request->getQueryString()]
        );
        
        foreach ($allInput as $value) {
            if (is_string($value) && $this->matchesSuspiciousPattern($value)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if value matches suspicious patterns
     */
    private function matchesSuspiciousPattern(string $value): bool
    {
        foreach ($this->suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $value)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Basic rate limiting implementation
     */
    private function isRateLimited(Request $request): bool
    {
        $ip = $request->ip();
        $key = 'waf_rate_limit_' . $ip;
        
        $requests = cache()->get($key, 0);
        
        if ($requests > 100) { // 100 requests per minute
            return true;
        }
        
        cache()->put($key, $requests + 1, now()->addMinute());
        
        return false;
    }

    /**
     * Log suspicious activity
     */
    private function logSuspiciousActivity(Request $request, string $reason): void
    {
        Log::warning('WAF: Suspicious activity detected', [
            'reason' => $reason,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'input' => $request->all(),
            'timestamp' => now(),
        ]);
    }
}
