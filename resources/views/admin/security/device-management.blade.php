@extends('admin.layouts.admin')

@section('title', 'Device Management')

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
                            <span class="text-green-400 font-medium">Device Management</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="admin-page-title">Device Management</h1>
            <p class="admin-page-description">Manage devices accessing your admin panel</p>
        </div>
        <div class="text-sm admin-text-muted">
            <span class="text-white font-medium">{{ $devices->count() }}</span> devices total
        </div>
    </div>


    <!-- Devices List -->
    <div class="admin-card">
        <div class="admin-section-title mb-6">
            <i class="fas fa-laptop text-green-400"></i>
            All Devices ({{ $devices->count() }})
        </div>

        @if($devices->count() > 0)
            <div class="space-y-4">
                @foreach($devices as $device)
                    <div class="p-6 bg-gray-700/20 rounded-lg {{ $device->device_id === $currentDeviceId ? 'ring-2 ring-blue-500/50 bg-blue-500/5' : '' }} hover:bg-gray-700/30 transition-colors">
                        <div class="flex items-start justify-between">
                            <!-- Device Info -->
                            <div class="flex items-start space-x-4 flex-1">
                                <!-- Icon -->
                                <div class="w-12 h-12 bg-{{ $device->status_color }}-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-{{ $device->device_type === 'mobile' ? 'mobile-alt' : ($device->device_type === 'tablet' ? 'tablet-alt' : 'laptop') }} text-{{ $device->status_color }}-400 text-lg"></i>
                                </div>

                                <!-- Details -->
                                <div class="flex-1">
                                    <!-- Device Name -->
                                    <div class="flex items-center gap-3 mb-3">
                                        <h3 class="text-lg font-semibold text-white">{{ $device->device_name }}</h3>
                                        @if($device->device_id === $currentDeviceId)
                                            <span class="px-2 py-1 bg-blue-500/20 text-blue-400 text-xs rounded-full border border-blue-500/30">
                                                <i class="fas fa-desktop mr-1"></i>Current
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Status -->
                                    <div class="flex items-center mb-4">
                                        <div class="w-2 h-2 bg-{{ $device->status_color }}-400 rounded-full mr-2"></div>
                                        <span class="text-{{ $device->status_color }}-400 text-sm font-medium">{{ $device->status_text }}</span>
                                    </div>

                                    <!-- Device Information -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-gray-300">
                                        <div class="flex items-center">
                                            <i class="fas fa-globe w-5 mr-2 admin-text-muted"></i>
                                            <span>{{ $device->browser }} • {{ $device->operating_system }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-map-marker-alt w-5 mr-2 admin-text-muted"></i>
                                            <span>{{ $device->ip_address }}@if($device->location) • {{ $device->location }}@endif</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-clock w-5 mr-2 admin-text-muted"></i>
                                            <span>Last: {{ $device->last_activity_human }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-calendar w-5 mr-2 admin-text-muted"></i>
                                            <span>First: {{ $device->first_seen->format('M d, Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="ml-6 text-right">
                                <!-- Action Buttons -->
                                @if($device->device_id !== $currentDeviceId)
                                    <div class="flex gap-2">
                                        @if(!$device->is_trusted && !$device->is_blocked)
                                            <form method="POST" action="{{ route('admin.security.device-trust', $device) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                                                    <i class="fas fa-shield-alt mr-2"></i>
                                                    Trust
                                                </button>
                                            </form>
                                        @endif

                                        @if($device->is_trusted)
                                            <form method="POST" action="{{ route('admin.security.device-untrust', $device) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors">
                                                    <i class="fas fa-times mr-2"></i>
                                                    Remove Trust
                                                </button>
                                            </form>
                                        @endif

                                        @if(!$device->is_blocked)
                                            <form method="POST" action="{{ route('admin.security.device-block', $device) }}" onsubmit="return confirm('Are you sure you want to block this device?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
                                                    <i class="fas fa-ban mr-2"></i>
                                                    Block
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.security.device-unblock', $device) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                                                    <i class="fas fa-check mr-2"></i>
                                                    Unblock
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @else
                                    <div class="px-4 py-2 bg-blue-500/10 border border-blue-500/30 rounded-lg">
                                        <div class="text-blue-400 text-sm font-medium">
                                            <i class="fas fa-lock mr-1"></i>current device
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>
        @else
            <div class="admin-empty-state py-12">
                <i class="fas fa-laptop admin-empty-icon"></i>
                <div class="admin-empty-title">No Devices Found</div>
                <div class="admin-empty-description">Device sessions will appear here once you access the admin panel</div>
            </div>
        @endif
    </div>
@endsection
