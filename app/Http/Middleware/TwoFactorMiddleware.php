<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        // If user not authenticated
        if (!$user) {
            return $next($request);
        }
        
        // Skip 2FA for logout and 2FA routes to prevent redirect loops
        if ($request->routeIs('admin.2fa.*') || $request->routeIs('admin.logout')) {
            return $next($request);
        }
        
        // Skip 2FA for login routes 
        if ($request->routeIs('admin.login*') || $request->routeIs('admin.register')) {
            return $next($request);
        }
        
        // If user doesn't have 2FA enabled, redirect to setup
        if (!$user->hasTwoFactorEnabled()) {
            $message = $request->is('admin*') 
                ? 'Please complete two-factor authentication setup to access admin panel.'
                : 'Please complete two-factor authentication setup to continue browsing.';
                
            return redirect()->route('admin.2fa.setup')
                ->with('warning', $message);
        }
        
        // If 2FA is enabled but not verified in current session
        if (!$request->session()->get('2fa_verified')) {
            $message = $request->is('admin*') 
                ? 'Please verify your two-factor authentication code to access admin panel.'
                : 'Please verify your two-factor authentication code to continue browsing.';
                
            return redirect()->route('admin.2fa.show-verify')
                ->with('warning', $message);
        }
        
        // All good - user is authenticated and 2FA verified
        return $next($request);
    }
}
