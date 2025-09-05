@extends('admin.layouts.admin')

@section('title', 'Security Settings')

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
                            <a href="{{ route('admin.security.overview') }}" class="admin-back-link">
                                Security
                            </a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-left text-gray-600 mx-2"></i>
                            <span class="text-purple-400 font-medium">Settings</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="admin-page-title">Security Settings</h1>
            <p class="admin-page-description">System-wide security monitoring and configuration</p>
        </div>
    </div>

    <!-- System Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="admin-card">
            <div class="admin-section-title">
                <i class="fas fa-chart-line text-blue-400"></i>
                <h3>System Stats</h3>
            </div>
            <div class="space-y-3 text-sm">
                <div class="admin-flex-between">
                    <span class="admin-text-muted">Total Users</span>
                    <span class="text-white font-medium">{{ $stats['total_users'] }}</span>
                </div>
                <div class="admin-flex-between">
                    <span class="admin-text-muted">Total Activities</span>
                    <span class="text-white font-medium">{{ number_format($stats['total_activities']) }}</span>
                </div>
                <div class="admin-flex-between">
                    <span class="admin-text-muted">Security Events</span>
                    <span class="text-white font-medium">{{ number_format($stats['total_security_events']) }}</span>
                </div>
                <div class="admin-flex-between">
                    <span class="admin-text-muted">Total Devices</span>
                    <span class="text-white font-medium">{{ $stats['total_devices'] }}</span>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 admin-card">
            <div class="admin-flex-between mb-4">
                <div class="admin-section-title mb-0">
                    <i class="fas fa-exclamation-triangle text-red-400"></i>
                    <h3>Suspicious Activity</h3>
                </div>
                <span class="admin-badge bg-red-500/20 text-red-400">
                    {{ $suspiciousEvents->count() }} alerts
                </span>
            </div>
            
            @if($suspiciousEvents->count() > 0)
                <div class="space-y-3 max-h-64 overflow-y-auto">
                    @foreach($suspiciousEvents as $suspicious)
                        <div class="flex items-center justify-between p-3 bg-red-500/10 border border-red-500/20 rounded-lg">
                            <div>
                                <p class="text-white font-medium">{{ $suspicious->ip_address }}</p>
                                <p class="text-sm admin-text-muted">Last: {{ \Carbon\Carbon::parse($suspicious->last_attempt)->diffForHumans() }}</p>
                            </div>
                            <div class="text-right">
                                <span class="text-red-400 font-bold">{{ $suspicious->attempts }}</span>
                                <p class="text-xs admin-text-muted">attempts</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="admin-empty-state py-6">
                    <i class="fas fa-shield-alt admin-empty-icon text-green-400"></i>
                    <div class="admin-empty-title text-green-400">All Clear</div>
                    <div class="admin-empty-description">No suspicious activity detected</div>
                </div>
            @endif
        </div>
    </div>

    <!-- Recent System Activities -->
    <div class="admin-content-container">
        <div class="admin-flex-between p-6 border-b border-gray-700">
            <h2 class="admin-section-title mb-0">
                <i class="fas fa-history text-blue-400"></i>
                Recent Activities
            </h2>
            <a href="{{ route('admin.security.activity-logs') }}" class="admin-back-link text-sm">View All</a>
        </div>

        @if($recentActivities->count() > 0)
            <div class="divide-y divide-gray-700">
                @foreach($recentActivities as $activity)
                    <div class="p-4 hover:bg-gray-700/20 transition-colors">
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-sm font-medium flex-shrink-0">
                                {{ substr($activity->user->name, 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="admin-flex-between">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-white font-medium">{{ $activity->user->name }}</p>
                                        <p class="text-gray-300 text-sm">{{ $activity->description ?: ucfirst(str_replace('_', ' ', $activity->action)) }}</p>
                                        <div class="flex items-center mt-1 text-xs admin-text-muted">
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ $activity->created_at->diffForHumans() }}
                                            <span class="mx-2">•</span>
                                            <span>{{ $activity->ip_address }}</span>
                                        </div>
                                    </div>
                                    <span class="admin-badge bg-blue-500/20 text-blue-400 ml-3">
                                        {{ ucfirst(str_replace('_', ' ', $activity->action)) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="admin-empty-state p-12">
                <i class="fas fa-history admin-empty-icon"></i>
                <div class="admin-empty-title">No Recent Activities</div>
                <div class="admin-empty-description">System activities will appear here as they occur</div>
            </div>
        @endif
    </div>

    <!-- Security Configuration Status -->
    <div class="admin-card">
        <div class="admin-section-title">
            <i class="fas fa-cog text-purple-400"></i>
            <h2>Security Configuration</h2>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Security Features -->
            <div>
                <h3 class="text-white font-medium mb-3">Active Security Features</h3>
                <div class="space-y-3">
                    <div class="admin-flex-between p-3 bg-gray-700/30 rounded-lg">
                        <span class="text-gray-300">Rate Limiting</span>
                        <span class="admin-badge bg-green-500/20 text-green-400">Active</span>
                    </div>
                    <div class="admin-flex-between p-3 bg-gray-700/30 rounded-lg">
                        <span class="text-gray-300">Device Tracking</span>
                        <span class="admin-badge bg-green-500/20 text-green-400">Active</span>
                    </div>
                    <div class="admin-flex-between p-3 bg-gray-700/30 rounded-lg">
                        <span class="text-gray-300">Session Security</span>
                        <span class="admin-badge bg-green-500/20 text-green-400">Active</span>
                    </div>
                </div>
            </div>

            <!-- Logging Features -->
            <div>
                <h3 class="text-white font-medium mb-3">Activity Logging</h3>
                <div class="space-y-3">
                    <div class="admin-flex-between p-3 bg-gray-700/30 rounded-lg">
                        <span class="text-gray-300">CMS Activity</span>
                        <span class="admin-badge bg-green-500/20 text-green-400">Active</span>
                    </div>
                    <div class="admin-flex-between p-3 bg-gray-700/30 rounded-lg">
                        <span class="text-gray-300">Security Events</span>
                        <span class="admin-badge bg-green-500/20 text-green-400">Active</span>
                    </div>
                    <div class="admin-flex-between p-3 bg-gray-700/30 rounded-lg">
                        <span class="text-gray-300">Device Monitoring</span>
                        <span class="admin-badge bg-green-500/20 text-green-400">Active</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
