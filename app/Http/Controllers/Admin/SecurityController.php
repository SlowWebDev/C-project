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
    public function overview()
    {
        $user = auth()->user();
        
        // Get recent security events
        $recentEvents = SecurityEvent::where('user_id', $user->id)
            ->latest('occurred_at')
            ->take(5)
            ->get();
        
        // Get active devices
        $activeDevices = DeviceSession::where('user_id', $user->id)
            ->where('is_blocked', false)
            ->latest('last_activity')
            ->take(3)
            ->get();
        
        // Get recent activities
        $recentActivities = ActivityLog::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();
        
        // Security stats
        $stats = [
            'total_devices' => DeviceSession::where('user_id', $user->id)->count(),
            'trusted_devices' => DeviceSession::where('user_id', $user->id)->where('is_trusted', true)->count(),
            'blocked_devices' => DeviceSession::where('user_id', $user->id)->where('is_blocked', true)->count(),
            'login_attempts_today' => SecurityEvent::where('user_id', $user->id)
                ->where('event_type', 'login')
                ->whereDate('occurred_at', today())
                ->count(),
            'failed_logins_today' => SecurityEvent::where('user_id', $user->id)
                ->where('event_type', 'failed_login')
                ->whereDate('occurred_at', today())
                ->count(),
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
        
        $oldHash = $user->password;
        $user->update([
            'password' => Hash::make($request->password)
        ]);
        
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
        
        $oldEmail = $user->email;
        $user->update(['email' => $request->email]);
        
        SecurityEvent::logEvent('email_change', SecurityEvent::STATUS_SUCCESS, $user->id, 'Email address was updated');
        
        return back()->with('success', 'Email updated successfully!');
    }
    
    public function activityLogs(Request $request)
    {
        $query = ActivityLog::where('user_id', auth()->id())->with('user');
        
        // Filter by action
        if ($request->has('action') && $request->action) {
            $query->where('action', 'like', '%' . $request->action . '%');
        }
        
        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $activities = $query->latest()->paginate(20);
        
        return view('admin.security.activity-logs', compact('activities'));
    }
    
    public function securityEvents(Request $request)
    {
        $query = SecurityEvent::where('user_id', auth()->id());
        
        // Filter by event type
        if ($request->has('event_type') && $request->event_type) {
            $query->where('event_type', $request->event_type);
        }
        
        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('occurred_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('occurred_at', '<=', $request->date_to);
        }
        
        $events = $query->latest('occurred_at')->paginate(20);
        
        return view('admin.security.security-events', compact('events'));
    }
    
    public function deviceManagement()
    {
        $devices = DeviceSession::where('user_id', auth()->id())
            ->latest('last_activity')
            ->get();
        
        $currentDeviceId = DeviceSession::getCurrentDeviceId();
        
        return view('admin.security.device-management', compact('devices', 'currentDeviceId'));
    }
    
    public function blockDevice(DeviceSession $device)
    {
        if ($device->user_id !== auth()->id()) {
            abort(403);
        }
        
        if ($device->isCurrentDevice()) {
            return back()->withErrors(['error' => 'You cannot block your current device.']);
        }
        
        $device->block();
        
        return back()->with('success', 'Device has been blocked successfully.');
    }
    
    public function unblockDevice(DeviceSession $device)
    {
        if ($device->user_id !== auth()->id()) {
            abort(403);
        }
        
        $device->update(['is_blocked' => false]);
        
        return back()->with('success', 'Device has been unblocked successfully.');
    }
    
    public function trustDevice(DeviceSession $device)
    {
        if ($device->user_id !== auth()->id()) {
            abort(403);
        }
        
        $device->trust();
        
        return back()->with('success', 'Device has been marked as trusted.');
    }
    
    public function untrustDevice(DeviceSession $device)
    {
        if ($device->user_id !== auth()->id()) {
            abort(403);
        }
        
        $device->update(['is_trusted' => false]);
        
        return back()->with('success', 'Device trust has been removed.');
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
