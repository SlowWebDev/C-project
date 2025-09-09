@extends('admin.layouts.admin')

@section('title', 'Security Events')

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
                            <span class="text-amber-400 font-medium">Security Events</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="admin-page-title">Security Events</h1>
            <p class="admin-page-description">Monitor security activities and events</p>
        </div>
        <div class="text-sm admin-text-muted">
            <span class="text-white font-medium">{{ $events->count() }}</span> events found
        </div>
    </div>

<!-- Filters -->
<div class="admin-card">
    <form method="GET" class="admin-form-row grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
        <div class="admin-form-group">
            <label for="event_type" class="admin-label">Event Type</label>
            <select id="event_type" name="event_type" class="admin-select">
                <option value="">All Events</option>
                <option value="login" {{ request('event_type') === 'login' ? 'selected' : '' }}>Login</option>
                <option value="logout" {{ request('event_type') === 'logout' ? 'selected' : '' }}>Logout</option>
                <option value="failed_login" {{ request('event_type') === 'failed_login' ? 'selected' : '' }}>Failed Login</option>
                <option value="password_change" {{ request('event_type') === 'password_change' ? 'selected' : '' }}>Password Change</option>
                <option value="email_change" {{ request('event_type') === 'email_change' ? 'selected' : '' }}>Email Change</option>
                <option value="device_registered" {{ request('event_type') === 'device_registered' ? 'selected' : '' }}>New Device</option>
                <option value="device_blocked" {{ request('event_type') === 'device_blocked' ? 'selected' : '' }}>Device Blocked</option>
            </select>
        </div>
        
        <div class="admin-form-group">
            <label for="status" class="admin-label">Status</label>
            <select id="status" name="status" class="admin-select">
                <option value="">All Status</option>
                <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Success</option>
                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                <option value="blocked" {{ request('status') === 'blocked' ? 'selected' : '' }}>Blocked</option>
            </select>
        </div>
        
        <div class="admin-form-group">
            <label for="date_from" class="admin-label">From Date</label>
            <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}" class="admin-input">
        </div>
        
        <div class="admin-form-group">
            <label for="date_to" class="admin-label">To Date</label>
            <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}" class="admin-input">
        </div>
        
        <div class="admin-form-group sm:col-span-2 lg:col-span-1">
            <label class="admin-label opacity-0 hidden sm:block">Actions</label>
            <div class="flex flex-col sm:flex-row gap-2">
                <button type="submit" class="admin-btn-primary flex items-center justify-center gap-2">
                    <i class="fas fa-search"></i>
                    <span>Filter</span>
                </button>
                <a href="{{ route('admin.security.security-events') }}" class="admin-btn-secondary flex items-center justify-center gap-2">
                    <i class="fas fa-times"></i>
                    <span>Clear</span>
                </a>
            </div>
        </div>
    </form>
</div>
    <!-- Security Events -->
    <div class="admin-card">
        <div class="admin-section-title mb-6">
            <i class="fas fa-shield-alt text-amber-400"></i>
            Security Events
        </div>

        @if($events->count() > 0)
            <div class="space-y-4">
                @foreach($events as $event)
                    <!-- Desktop/Tablet Layout -->
                    <div class="hidden sm:block p-6 bg-gray-700/20 rounded-lg hover:bg-gray-700/30 transition-colors">
                        <div class="flex items-start justify-between">
                            <!-- Event Info -->
                            <div class="flex items-start space-x-4 flex-1">
                                <!-- Event Icon -->
                                <div class="w-12 h-12 bg-amber-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-shield-alt text-amber-400 text-lg"></i>
                                </div>

                                <!-- Event Details -->
                                <div class="flex-1">
                                    <!-- Event Title -->
                                    <div class="flex items-center gap-3 mb-3">
                                        <h3 class="text-lg font-semibold text-white">{{ $event->user->name ?? 'Unknown User' }}</h3>
                                        <span class="px-2 py-1 bg-amber-500/20 text-amber-400 text-xs rounded-full border border-amber-500/30">
                                            {{ $event->event_type_display }}
                                        </span>
                                    </div>

                                    <!-- Status -->
                                    <div class="flex items-center mb-4">
                                        <div class="w-2 h-2 bg-{{ $event->status_color }}-400 rounded-full mr-2"></div>
                                        <span class="text-{{ $event->status_color }}-400 text-sm font-medium">{{ ucfirst($event->status) }}</span>
                                        @if($event->description)
                                            <span class="text-gray-400 text-sm ml-2">• {{ $event->description }}</span>
                                        @endif
                                    </div>

                                    <!-- Event Information -->
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 text-sm text-gray-300">
                                        <div class="flex items-center">
                                            <i class="fas fa-clock w-5 mr-2 admin-text-muted"></i>
                                            <span>{{ $event->occurred_at->format('M d, Y') }} at {{ $event->occurred_at->format('g:i A') }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-map-marker-alt w-5 mr-2 admin-text-muted"></i>
                                            <span class="truncate">{{ $event->ip_address }}@if($event->location) • {{ $event->location }}@endif</span>
                                        </div>
                                        @if($event->device_info && isset($event->device_info['browser']))
                                            <div class="flex items-center">
                                                <i class="fas fa-globe w-5 mr-2 admin-text-muted"></i>
                                                <span class="truncate">{{ $event->device_info['browser'] }}@if(isset($event->device_info['platform'])) • {{ $event->device_info['platform'] }}@endif</span>
                                            </div>
                                        @endif
                                        @if($event->device_info && isset($event->device_info['device_type']))
                                            <div class="flex items-center">
                                                <i class="fas fa-laptop w-5 mr-2 admin-text-muted"></i>
                                                <span class="capitalize">{{ $event->device_info['device_type'] }} Device</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="ml-6 text-right">
                                <div class="flex gap-2">
                                    <a href="https://whatismyipaddress.com/ip/{{ $event->ip_address }}" 
                                       target="_blank" 
                                       class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors"
                                       title="View IP location details">
                                        <i class="fas fa-map-marker-alt mr-2"></i>Locate IP
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Mobile Layout -->
                    <div class="sm:hidden p-4 bg-gray-700/20 rounded-lg">
                        <!-- Header Row -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-amber-500/20 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-shield-alt text-amber-400"></i>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-white font-medium text-base">{{ \Str::limit($event->user->name ?? 'Unknown User', 20) }}</h3>
                                    <div class="flex items-center mt-1">
                                        <div class="w-2 h-2 bg-{{ $event->status_color }}-400 rounded-full mr-2"></div>
                                        <span class="text-{{ $event->status_color }}-400 text-sm">{{ ucfirst($event->status) }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <span class="px-2 py-1 bg-amber-500/20 text-amber-400 text-xs rounded-full border border-amber-500/30">
                                {{ \Str::limit($event->event_type_display, 12) }}
                            </span>
                        </div>
                        
                        <!-- Event Info Grid -->
                        <div class="space-y-3 mb-4">
                            <div class="flex items-center text-sm text-gray-300">
                                <i class="fas fa-clock w-4 mr-2 text-gray-400"></i>
                                <span>{{ $event->occurred_at->format('M d, Y') }} - {{ $event->occurred_at->format('g:i A') }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-300">
                                <i class="fas fa-map-marker-alt w-4 mr-2 text-gray-400"></i>
                                <span class="truncate">{{ $event->ip_address }}</span>
                            </div>
                            @if($event->device_info && isset($event->device_info['browser']))
                                <div class="flex items-center text-sm text-gray-300">
                                    <i class="fas fa-globe w-4 mr-2 text-gray-400"></i>
                                    <span class="truncate">{{ $event->device_info['browser'] }}</span>
                                </div>
                            @endif
                            @if($event->description)
                                <div class="flex items-center text-sm text-gray-300">
                                    <i class="fas fa-info-circle w-4 mr-2 text-gray-400"></i>
                                    <span class="truncate">{{ $event->description }}</span>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Action Button -->
                        <div class="border-t border-gray-600 pt-4">
                            <a href="https://whatismyipaddress.com/ip/{{ $event->ip_address }}" 
                               target="_blank" 
                               class="w-full px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors text-center inline-block">
                                <i class="fas fa-map-marker-alt mr-2"></i>Locate IP Address
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="p-4 border-t border-gray-700">
                {{ $events->appends(request()->query())->links() }}
            </div>
        @else
            <div class="admin-empty-state p-12">
                <i class="fas fa-shield-alt admin-empty-icon"></i>
                <div class="admin-empty-title">No Security Events Found</div>
                <div class="admin-empty-description">
                    @if(request()->hasAny(['event_type', 'status', 'date_from', 'date_to']))
                        No events match your current filters
                    @else
                        No security events have been recorded yet
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

@endsection
