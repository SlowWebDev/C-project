@extends('admin.layouts.admin')

@section('title', 'Security Center')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="admin-page-header">
        <div>
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('admin.dashboard') }}" class="admin-back-link">
                            <i class="fas fa-home mr-2"></i>Dashboard
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-left text-gray-600 mx-2"></i>
                            <span class="text-blue-400 font-medium">Security</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="admin-page-title">Security Center</h1>
            <p class="admin-page-description">Monitor and manage your admin panel security</p>
        </div>
    </div>

    <!-- Security Overview -->
    <div class="admin-card mb-6">
        <!-- Security Stats -->
        <div class="mb-6">
            <h3 class="admin-section-title mb-4">
                <i class="fas fa-chart-bar text-blue-400"></i>
                Security Statistics
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="flex items-center p-4 bg-gray-700/30 rounded-lg">
                    <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-laptop text-blue-400"></i>
                    </div>
                    <div>
                        <div class="text-lg font-bold text-white">{{ $stats['total_devices'] }}</div>
                        <div class="text-xs admin-text-muted">Total Devices</div>
                    </div>
                </div>
                
                <div class="flex items-center p-4 bg-gray-700/30 rounded-lg">
                    <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-shield-alt text-green-400"></i>
                    </div>
                </div>
                
                <div class="flex items-center p-4 bg-gray-700/30 rounded-lg">
                    <div class="w-10 h-10 bg-amber-500/20 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-sign-in-alt text-amber-400"></i>
                    </div>
                    <div>
                        <div class="text-lg font-bold text-white">{{ $stats['login_attempts_today'] }}</div>
                        <div class="text-xs admin-text-muted">Logins Today</div>
                    </div>
                </div>
                
                <div class="flex items-center p-4 bg-gray-700/30 rounded-lg">
                    <div class="w-10 h-10 bg-red-500/20 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-exclamation-triangle text-red-400"></i>
                    </div>
                    <div>
                        <div class="text-lg font-bold text-white">{{ $stats['failed_logins_today'] }}</div>
                        <div class="text-xs admin-text-muted">Failed Logins</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Divider -->
        <div class="admin-divider"></div>

        <!-- Quick Actions -->
        <div>
            <h3 class="admin-section-title mb-4">
                <i class="fas fa-link text-blue-400"></i>
                Quick Actions
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                <a href="{{ route('admin.security.activity-logs') }}" class="flex items-center p-3 bg-gray-700/20 hover:bg-gray-700/40 rounded-lg transition-colors group">
                    <div class="w-8 h-8 bg-blue-500/20 rounded-lg flex items-center justify-center mr-3 group-hover:bg-blue-500/30 transition-colors">
                        <i class="fas fa-history text-blue-400"></i>
                    </div>
                    <div class="text-sm text-white group-hover:text-blue-300 transition-colors">Activity Logs</div>
                </a>
                
                <a href="{{ route('admin.security.security-events') }}" class="flex items-center p-3 bg-gray-700/20 hover:bg-gray-700/40 rounded-lg transition-colors group">
                    <div class="w-8 h-8 bg-amber-500/20 rounded-lg flex items-center justify-center mr-3 group-hover:bg-amber-500/30 transition-colors">
                        <i class="fas fa-shield-alt text-amber-400"></i>
                    </div>
                    <div class="text-sm text-white group-hover:text-amber-300 transition-colors">Security Events</div>
                </a>
                
                <a href="{{ route('admin.security.device-management') }}" class="flex items-center p-3 bg-gray-700/20 hover:bg-gray-700/40 rounded-lg transition-colors group">
                    <div class="w-8 h-8 bg-green-500/20 rounded-lg flex items-center justify-center mr-3 group-hover:bg-green-500/30 transition-colors">
                        <i class="fas fa-laptop text-green-400"></i>
                    </div>
                    <div class="text-sm text-white group-hover:text-green-300 transition-colors">Device Management</div>
                </a>
                
                @if(auth()->id() === 1)
                <a href="{{ route('admin.security.settings') }}" class="flex items-center p-3 bg-gray-700/20 hover:bg-gray-700/40 rounded-lg transition-colors group">
                    <div class="w-8 h-8 bg-purple-500/20 rounded-lg flex items-center justify-center mr-3 group-hover:bg-purple-500/30 transition-colors">
                        <i class="fas fa-cog text-purple-400"></i>
                    </div>
                    <div class="text-sm text-white group-hover:text-purple-300 transition-colors">Security Settings</div>
                </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Account Settings -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Password Change -->
        <div class="admin-card">
            <div class="admin-section-title">
                <i class="fas fa-key text-blue-400"></i>
                <h2>Change Password</h2>
            </div>
            
            <form action="{{ route('admin.security.update-password') }}" method="POST" class="space-y-3">
                @csrf
                @method('PATCH')
                
                <div class="admin-form-group">
                    <label for="current_password" class="admin-label admin-label-required">Current Password</label>
                    <div class="relative">
                        <input type="password" id="current_password" name="current_password" required 
                               class="admin-input peer"
                               oninput="this.nextElementSibling.value = this.value">
                        <input type="text" 
                               class="admin-input absolute inset-0 opacity-0 peer-focus:opacity-100 focus:opacity-100"
                               oninput="this.previousElementSibling.value = this.value">
                    </div>
                    @error('current_password')
                        <p class="admin-error-message"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="admin-form-group">
                    <label for="password" class="admin-label admin-label-required">New Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required 
                               class="admin-input peer"
                               oninput="this.nextElementSibling.value = this.value">
                        <input type="text" 
                               class="admin-input absolute inset-0 opacity-0 peer-focus:opacity-100 focus:opacity-100"
                               oninput="this.previousElementSibling.value = this.value">
                    </div>
                    @error('password')
                        <p class="admin-error-message"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="admin-form-group">
                    <label for="password_confirmation" class="admin-label admin-label-required">Confirm Password</label>
                    <div class="relative">
                        <input type="password" id="password_confirmation" name="password_confirmation" required 
                               class="admin-input peer"
                               oninput="this.nextElementSibling.value = this.value">
                        <input type="text" 
                               class="admin-input absolute inset-0 opacity-0 peer-focus:opacity-100 focus:opacity-100"
                               oninput="this.previousElementSibling.value = this.value">
                    </div>
                </div>
                
                <button type="submit" class="admin-btn-primary w-full">
                    <i class="fas fa-save"></i>Update Password
                </button>
            </form>
        </div>

        <!-- Email Change -->
        <div class="admin-card">
            <div class="admin-section-title">
                <i class="fas fa-envelope text-blue-400"></i>
                <h2>Change Email</h2>
            </div>
            
            <form action="{{ route('admin.security.update-email') }}" method="POST" class="space-y-3">
                @csrf
                @method('PATCH')
                
                <div class="admin-form-group">
                    <label for="current_email" class="admin-label">Current Email</label>
                    <input type="email" id="current_email" value="{{ auth()->user()->email }}" disabled class="admin-input opacity-50">
                </div>
                
                <div class="admin-form-group">
                    <label for="email" class="admin-label admin-label-required">New Email</label>
                    <input type="email" id="email" name="email" required value="{{ old('email') }}" class="admin-input">
                    @error('email')
                        <p class="admin-error-message"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="admin-form-group">
                    <label for="email_current_password" class="admin-label admin-label-required">Current Password</label>
                    <input type="password" id="email_current_password" name="current_password" required class="admin-input">
                    @error('current_password')
                        <p class="admin-error-message"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                    @enderror
                </div>
                
                <button type="submit" class="admin-btn-primary w-full">
                    <i class="fas fa-save"></i>Update Email
                </button>
            </form>
        </div>
    </div>
    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Recent Security Events -->
        <div class="admin-card">
            <div class="admin-flex-between mb-3">
                <h2 class="admin-section-title mb-0">
                    <i class="fas fa-shield-alt text-amber-400"></i>
                    Recent Events
                </h2>
                <a href="{{ route('admin.security.security-events') }}" class="admin-back-link text-xs">View All</a>
            </div>
            
            @if($recentEvents->count() > 0)
                <div class="space-y-2">
                    @foreach($recentEvents->take(4) as $event)
                        <div class="flex items-center p-2 bg-gray-700/30 rounded">
                            <div class="w-2 h-2 bg-{{ $event->status_color }}-400 rounded-full mr-2"></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs text-white truncate">{{ $event->event_type_display }}</p>
                                <p class="text-xs admin-text-muted">{{ $event->occurred_at->diffForHumans() }}</p>
                            </div>
                            <span class="text-xs px-2 py-1 bg-{{ $event->status_color }}-500/20 text-{{ $event->status_color }}-400 rounded">
                                {{ ucfirst($event->status) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-shield-alt text-gray-500 text-2xl mb-2"></i>
                    <p class="text-xs admin-text-muted">No security events recorded yet</p>
                </div>
            @endif
        </div>

        <!-- Active Devices -->
        <div class="admin-card">
            <div class="admin-flex-between mb-3">
                <h2 class="admin-section-title mb-0">
                    <i class="fas fa-laptop text-green-400"></i>
                    Active Devices
                </h2>
                <a href="{{ route('admin.security.device-management') }}" class="admin-back-link text-xs">Manage All</a>
            </div>
            
            @if($activeDevices->count() > 0)
                <div class="space-y-2">
                    @foreach($activeDevices->take(4) as $device)
                        <div class="flex items-center p-2 bg-gray-700/30 rounded">
                            <div class="w-2 h-2 bg-{{ $device->status_color }}-400 rounded-full mr-2"></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs text-white truncate">{{ $device->device_name }}</p>
                                <p class="text-xs admin-text-muted">{{ $device->last_activity_human }}</p>
                            </div>
                            <span class="text-xs px-2 py-1 bg-{{ $device->status_color }}-500/20 text-{{ $device->status_color }}-400 rounded">
                                {{ $device->status_text }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-laptop text-gray-500 text-2xl mb-2"></i>
                    <p class="text-xs admin-text-muted">No active devices found</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
