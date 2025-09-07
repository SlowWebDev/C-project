<?php

namespace App\Http\Middleware;

use App\Models\DeviceSession;
use App\Models\SecurityEvent;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            // Check session timeout (4 hours)
            $lastActivity = $request->session()->get('last_activity');
            if ($lastActivity && now()->diffInHours($lastActivity) > 4) {
                SecurityEvent::logEvent(
                    'session_timeout',
                    SecurityEvent::STATUS_SUCCESS,
                    $user->id,
                    'User session expired after 4 hours of inactivity'
                );
                
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect()->route('admin.login')
                    ->with('warning', 'Your session has expired. Please log in again.');
            }
            
            // Update last activity
            $request->session()->put('last_activity', now());
            
            $device = DeviceSession::registerOrUpdateDevice();
            
            // Check if device is blocked
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
            
            // Log admin panel access
            if ($request->is('admin') || $request->is('admin/*')) {
                $this->logAdminAccess($request);
            }
        }
        
        return $next($request);
    }
    
    private function logAdminAccess(Request $request)
    {
        // Avoid logging too frequently (once per session)
        $sessionKey = 'admin_access_logged_' . auth()->id();
        
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
