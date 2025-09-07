<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\SecurityEvent;
use App\Models\DeviceSession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class SecurityController extends Controller
{
    private function getCurrentUser()
    {
        return auth()->user()->load(['securityEvents', 'deviceSessions', 'activityLogs']);
    }

    public function overview()
    {
        $user = $this->getCurrentUser();
        
        $recentEvents = $user->securityEvents()
            ->latest('occurred_at')
            ->take(5)
            ->get();
        
        $activeDevices = $user->deviceSessions()
            ->where('is_blocked', false)
            ->latest('last_activity')
            ->take(3)
            ->get();
        
        $recentActivities = $user->activityLogs()
            ->latest()
            ->take(5)
            ->get();
        
        $deviceStats = $user->deviceSessions()
            ->selectRaw('COUNT(*) as total, SUM(is_trusted) as trusted, SUM(is_blocked) as blocked')
            ->first();
        
        $loginStats = $user->securityEvents()
            ->whereDate('occurred_at', today())
            ->selectRaw('SUM(CASE WHEN event_type = "login" THEN 1 ELSE 0 END) as login_attempts,
                        SUM(CASE WHEN event_type = "failed_login" THEN 1 ELSE 0 END) as failed_logins')
            ->first();

        $stats = [
            'total_devices' => $deviceStats->total ?? 0,
            'trusted_devices' => $deviceStats->trusted ?? 0,
            'blocked_devices' => $deviceStats->blocked ?? 0,
            'login_attempts_today' => $loginStats->login_attempts ?? 0,
            'failed_logins_today' => $loginStats->failed_logins ?? 0,
        ];
        
        return view('admin.security.overview', compact('recentEvents', 'activeDevices', 'recentActivities', 'stats'));
    }
    
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);
        
        $user = auth()->user();
        
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }
        
        $user->update(['password' => Hash::make($request->password)]);
        
        SecurityEvent::logEvent('password_change', SecurityEvent::STATUS_SUCCESS, $user->id, 'Password changed successfully');
        
        return back()->with('success', 'Password updated successfully!');
    }
    
    public function updateEmail(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'current_password' => 'required',
        ]);
        
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }
        
        $user->update(['email' => $request->email]);
        
        SecurityEvent::logEvent('email_change', SecurityEvent::STATUS_SUCCESS, $user->id, 'Email address was updated');
        
        return back()->with('success', 'Email updated successfully!');
    }
    
    public function activityLogs(Request $request)
    {
        $user = auth()->user();
        $query = $user->activityLogs()->with('user');
        
        if ($request->filled('action')) {
            $query->where('action', 'like', '%' . $request->action . '%');
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $activities = $query->latest()->paginate(20);
        
        return view('admin.security.activity-logs', compact('activities'));
    }
    
    public function securityEvents(Request $request)
    {
        $user = auth()->user();
        $query = $user->securityEvents();
        
        if ($request->filled('event_type')) {
            $query->where('event_type', $request->event_type);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('occurred_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('occurred_at', '<=', $request->date_to);
        }
        
        $events = $query->latest('occurred_at')->paginate(20);
        
        return view('admin.security.security-events', compact('events'));
    }
    
    public function deviceManagement()
    {
        $user = auth()->user();
        $devices = $user->deviceSessions()
            ->latest('last_activity')
            ->get();
        
        $currentDeviceId = DeviceSession::getCurrentDeviceId();
        
        return view('admin.security.device-management', compact('devices', 'currentDeviceId'));
    }
    
    public function blockDevice(DeviceSession $device)
    {
        $this->authorizeDevice($device);
        
        if ($device->isCurrentDevice()) {
            return back()->withErrors(['error' => 'You cannot block your current device.']);
        }
        
        $device->block();
        
        return back()->with('success', 'Device has been blocked successfully.');
    }
    
    public function unblockDevice(DeviceSession $device)
    {
        $this->authorizeDevice($device);
        
        $device->update(['is_blocked' => false]);
        
        return back()->with('success', 'Device has been unblocked successfully.');
    }
    
    public function trustDevice(DeviceSession $device)
    {
        $this->authorizeDevice($device);
        
        $device->trust();
        
        return back()->with('success', 'Device has been marked as trusted.');
    }
    
    public function untrustDevice(DeviceSession $device)
    {
        $this->authorizeDevice($device);
        
        $device->update(['is_trusted' => false]);
        
        return back()->with('success', 'Device trust has been removed.');
    }
    
    private function authorizeDevice(DeviceSession $device)
    {
        if ($device->user_id !== auth()->id()) {
            abort(403);
        }
    }

    public function settings()
    {
        // Only accessible by main admin
        if (auth()->id() !== 1) {
            abort(403, 'Access denied. Admin only.');
        }
        
        $stats = [
            'total_users' => User::count(),
            'total_activities' => ActivityLog::count(),
            'total_security_events' => SecurityEvent::count(),
            'total_devices' => DeviceSession::count(),
            'blocked_devices' => DeviceSession::where('is_blocked', true)->count(),
            'recent_logins' => SecurityEvent::where('event_type', 'login')
                ->where('status', 'success')
                ->whereDate('occurred_at', today())
                ->count(),
            'failed_logins_today' => SecurityEvent::where('event_type', 'failed_login')
                ->whereDate('occurred_at', today())
                ->count(),
        ];
        
        $recentActivities = ActivityLog::with('user')
            ->latest()
            ->take(10)
            ->get();
        
        $suspiciousEvents = SecurityEvent::where('event_type', 'failed_login')
            ->where('occurred_at', '>=', now()->subHours(24))
            ->groupBy('ip_address')
            ->havingRaw('COUNT(*) >= 3')
            ->selectRaw('ip_address, COUNT(*) as attempts, MAX(occurred_at) as last_attempt')
            ->orderBy('attempts', 'desc')
            ->get();
        
        return view('admin.security.settings', compact('stats', 'recentActivities', 'suspiciousEvents'));
    }
}
