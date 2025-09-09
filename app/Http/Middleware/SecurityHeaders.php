<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Security Headers Middleware - HTTP Security Headers
 * 
 * Adds security headers to protect against XSS, clickjacking, and other attacks
 * 
 * @author SlowWebDev
 */
class SecurityHeaders
{
    /**
     * Add security headers to all responses
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Content Security Policy
        $response->headers->set('Content-Security-Policy', "
            default-src 'self';
            script-src 'self' 'nonce-'.base64_encode(random_bytes(16));
            style-src 'self' 'unsafe-inline';
            img-src 'self' data: https:;
            font-src 'self' data:;
            frame-ancestors 'none';
            form-action 'self';
        ");

        // Prevent clickjacking
        $response->headers->set('X-Frame-Options', 'DENY');

        // Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Enable XSS protection
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Force HTTPS
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

        // Referrer Policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions Policy
        $response->headers->set('Permissions-Policy', 'accelerometer=(), camera=(), geolocation=(), gyroscope=(), magnetometer=(), microphone=(), payment=(), usb=()');

        // Remove X-Powered-By header
        $response->headers->remove('X-Powered-By');
        
        return $response;
    }
}
