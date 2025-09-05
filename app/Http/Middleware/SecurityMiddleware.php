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
            $device = DeviceSession::registerOrUpdateDevice();
            
            // Check if device is blocked
            if ($device->is_blocked) {
                SecurityEvent::logEvent(
                    'blocked_device_attempt', 
                    SecurityEvent::STATUS_BLOCKED,
                    auth()->id(),
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
