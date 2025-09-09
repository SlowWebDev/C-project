<?php

namespace App\Http\Middleware;

use App\Models\DeviceSession;
use App\Models\SecurityEvent;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Security Middleware - Admin Panel Protection
 * 
 * Handles session timeouts, device tracking, and security event logging
 * 
 * @author SlowWebDev
 */
class SecurityMiddleware
{
    /**
     * Handle security checks for admin panel access
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            // Check session timeout (4 hours of inactivity)
            $lastActivity = $request->session()->get('last_activity');
            if ($lastActivity && now()->diffInHours($lastActivity) > 4) {
                SecurityEvent::logEvent(
                    'session_timeout',
                    SecurityEvent::STATUS_SUCCESS,
                    $user->id,
                    'User session expired after 4 hours of inactivity'
                );
                
                // Force logout and clean session
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect()->route('admin.login')
                    ->with('warning', 'Your session has expired. Please log in again.');
            }
            
            // Update last activity timestamp
            $request->session()->put('last_activity', now());
            
            // Register or update current device
            $device = DeviceSession::registerOrUpdateDevice();
            
            // Block access if device is blacklisted
            if ($device->is_blocked) {
                SecurityEvent::logEvent(
                    'blocked_device_attempt', 
                    SecurityEvent::STATUS_BLOCKED,
                    $user->id,
                    'Blocked device attempted to access admin panel'
                );
                
                auth()->logout();
                return redirect()->route('admin.login')
                    ->withErrors(['error' => 'This device has been blocked. Please contact administrator.']);
            }
            
            // Log admin panel access for security audit
            if ($request->is('admin') || $request->is('admin/*')) {
                $this->logAdminAccess($request);
            }
        }
        
        return $next($request);
    }
    
    /**
     * Log admin panel access for security monitoring
     * Prevents excessive logging by limiting to once per 30 minutes per session
     */
    private function logAdminAccess(Request $request)
    {
        $sessionKey = 'admin_access_logged_' . auth()->id();
        
        // Only log if not logged recently (30 min window)
        if (!session()->has($sessionKey) || session($sessionKey) < now()->subMinutes(30)) {
            SecurityEvent::logEvent(
                'admin_panel_access',
                SecurityEvent::STATUS_SUCCESS,
                auth()->id(),
                'User accessed admin panel from ' . $request->path()
            );
            
            session([$sessionKey => now()]);
        }
    }
}
