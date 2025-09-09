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
            ->selectRaw('COUNT(*) as total, SUM(is_blocked) as blocked')
            ->first();
        
        $loginStats = $user->securityEvents()
            ->whereDate('occurred_at', today())
            ->selectRaw('SUM(CASE WHEN event_type = "login" THEN 1 ELSE 0 END) as login_attempts,
                        SUM(CASE WHEN event_type = "failed_login" THEN 1 ELSE 0 END) as failed_logins')
            ->first();

        $stats = [
            'total_devices' => $deviceStats->total ?? 0,
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
    
    public function blockDevice(Request $request, DeviceSession $device)
    {
        $this->authorizeDevice($device);
        
        if ($device->isCurrentDevice()) {
            return back()->withErrors(['error' => 'You cannot block your current device.']);
        }
        
        // Check if 2FA code is provided
        if (!$request->has('tfa_code')) {
            // Store device info in session for 2FA verification
            session([
                '2fa_pending_action' => 'block_device',
                '2fa_device_id' => $device->id,
                '2fa_device_name' => $device->device_name,
                '2fa_return_url' => url()->previous()
            ]);
            
            return redirect()->route('admin.security.verify-2fa')
                ->with('info', 'Please verify your identity to block this device.');
        }
        
        // Verify 2FA code
        if (!$this->verify2FA($request->tfa_code)) {
            return back()->withErrors(['tfa_code' => 'Invalid authentication code. Please try again.']);
        }
        
        $device->block();
        
        // Clear 2FA session data
        session()->forget(['2fa_pending_action', '2fa_device_id', '2fa_device_name', '2fa_return_url']);
        
        return back()->with('success', 'Device has been blocked successfully.');
    }
    
    public function unblockDevice(Request $request, DeviceSession $device)
    {
        $this->authorizeDevice($device);
        
        // Check if 2FA code is provided
        if (!$request->has('tfa_code')) {
            // Store device info in session for 2FA verification
            session([
                '2fa_pending_action' => 'unblock_device',
                '2fa_device_id' => $device->id,
                '2fa_device_name' => $device->device_name,
                '2fa_return_url' => url()->previous()
            ]);
            
            return redirect()->route('admin.security.verify-2fa')
                ->with('info', 'Please verify your identity to unblock this device.');
        }
        
        // Verify 2FA code
        if (!$this->verify2FA($request->tfa_code)) {
            return back()->withErrors(['tfa_code' => 'Invalid authentication code. Please try again.']);
        }
        
        $device->update(['is_blocked' => false]);
        
        // Clear 2FA session data
        session()->forget(['2fa_pending_action', '2fa_device_id', '2fa_device_name', '2fa_return_url']);
        
        return back()->with('success', 'Device has been unblocked successfully.');
    }
    
    
    public function show2FAVerification()
    {
        // Check if there's a pending 2FA action
        if (!session('2fa_pending_action')) {
            return redirect()->route('admin.security.device-management')
                ->with('error', 'No pending security action found.');
        }
        
        $pendingAction = session('2fa_pending_action');
        $deviceName = session('2fa_device_name');
        $actionText = $pendingAction === 'block_device' ? 'Block Device' : 'Unblock Device';
        
        return view('admin.security.verify-2fa', compact('pendingAction', 'deviceName', 'actionText'));
    }
    
    public function process2FAVerification(Request $request)
    {
        $user = auth()->user();
        
        // Dynamic validation based on 2FA setup
        if ($user->hasTwoFactorEnabled()) {
            $request->validate([
                'tfa_code' => 'required|string|size:6'
            ]);
        } else {
            $request->validate([
                'password' => 'required|string'
            ]);
        }
        
        // Check if there's a pending action
        if (!session('2fa_pending_action')) {
            return redirect()->route('admin.security.device-management')
                ->with('error', 'No pending security action found.');
        }
        
        // Verify authentication
        if ($user->hasTwoFactorEnabled()) {
            // Use 2FA verification
            if (!$this->verify2FA($request->tfa_code)) {
                return back()->withErrors(['tfa_code' => 'Invalid authentication code. Please try again.']);
            }
        } else {
            // Use password verification as fallback
            if (!Hash::check($request->password, $user->password)) {
                return back()->withErrors(['password' => 'Invalid password. Please try again.']);
            }
        }
        
        // Get pending action details
        $action = session('2fa_pending_action');
        $deviceId = session('2fa_device_id');
        $returnUrl = session('2fa_return_url', route('admin.security.device-management'));
        
        // Find device
        $device = DeviceSession::find($deviceId);
        if (!$device || $device->user_id !== auth()->id()) {
            session()->forget(['2fa_pending_action', '2fa_device_id', '2fa_device_name', '2fa_return_url']);
            return redirect()->route('admin.security.device-management')
                ->with('error', 'Device not found or access denied.');
        }
        
        // Execute the pending action
        try {
            if ($action === 'block_device') {
                if ($device->isCurrentDevice()) {
                    throw new \Exception('You cannot block your current device.');
                }
                $device->block();
                $message = 'Device has been blocked successfully.';
            } elseif ($action === 'unblock_device') {
                $device->update(['is_blocked' => false]);
                $message = 'Device has been unblocked successfully.';
            } else {
                throw new \Exception('Invalid action.');
            }
            
            // Clear session data
            session()->forget(['2fa_pending_action', '2fa_device_id', '2fa_device_name', '2fa_return_url']);
            
            return redirect($returnUrl)->with('success', $message);
            
        } catch (\Exception $e) {
            session()->forget(['2fa_pending_action', '2fa_device_id', '2fa_device_name', '2fa_return_url']);
            return redirect($returnUrl)->with('error', $e->getMessage());
        }
    }
    
    private function verify2FA($code)
    {
        $user = auth()->user();
        
        // Check if user has 2FA enabled
        if (!$user->hasTwoFactorEnabled()) {
            return false;
        }
        
        try {
            // Use the same method as login verification
            return $user->verifyTwoFactorCode($code, false);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('2FA verification error: ' . $e->getMessage());
            return false;
        }
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
