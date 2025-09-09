@extends('admin.layouts.admin')

@section('title', 'Activity Logs')

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
                            <span class="text-blue-400 font-medium">Activity Logs</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="admin-page-title">Activity Logs</h1>
            <p class="admin-page-description">Track all system activities and changes</p>
        </div>
        <div class="text-sm admin-text-muted">
            <span class="text-white font-medium">{{ $activities->count() }}</span> activities found
        </div>
    </div>

    <!-- Filters -->
    <div class="admin-card">
        <form method="GET" class="admin-form-row grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="admin-form-group">
                <label for="action" class="admin-label">Search Actions</label>
                <input type="text" id="action" name="action" value="{{ request('action') }}" 
                       placeholder="Search actions..." class="admin-input">
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
                    <a href="{{ route('admin.security.activity-logs') }}" class="admin-btn-secondary flex items-center justify-center gap-2">
                        <i class="fas fa-times"></i>
                        <span>Clear</span>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Activity Logs -->
    <div class="admin-card">
        <div class="admin-section-title mb-6">
            <i class="fas fa-history text-blue-400"></i>
            Activity History
        </div>

        @if($activities->count() > 0)
            <div class="space-y-4">
                @foreach($activities as $activity)
                    <!-- Desktop/Tablet Layout -->
                    <div class="hidden sm:block p-6 bg-gray-700/20 rounded-lg hover:bg-gray-700/30 transition-colors">
                        <div class="flex items-start space-x-4">
                            <!-- Activity Icon -->
                            <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-history text-blue-400 text-lg"></i>
                            </div>

                            <!-- Activity Details -->
                            <div class="flex-1">
                                <!-- Activity Title -->
                                <div class="flex items-center gap-3 mb-3">
                                    <h3 class="text-lg font-semibold text-white">{{ $activity->user->name ?? 'Unknown User' }}</h3>
                                    <span class="px-2 py-1 bg-blue-500/20 text-blue-400 text-xs rounded-full border border-blue-500/30">
                                        {{ ucfirst(str_replace('_', ' ', $activity->action)) }}
                                    </span>
                                </div>

                                <!-- Description -->
                                <div class="mb-4">
                                    <p class="text-gray-300 text-base leading-relaxed">
                                        {{ $activity->description ?: ucfirst(str_replace('_', ' ', $activity->action)) }}
                                    </p>
                                </div>

                                <!-- Activity Information -->
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 text-sm text-gray-300">
                                    <div class="flex items-center">
                                        <i class="fas fa-clock w-5 mr-2 admin-text-muted"></i>
                                        <span>{{ $activity->created_at->format('M d, Y') }} at {{ $activity->created_at->format('g:i A') }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-map-marker-alt w-5 mr-2 admin-text-muted"></i>
                                        <span class="truncate">{{ $activity->ip_address }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Mobile Layout -->
                    <div class="sm:hidden p-4 bg-gray-700/20 rounded-lg">
                        <!-- Header Row -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-history text-blue-400"></i>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-white font-medium text-base">{{ \Str::limit($activity->user->name ?? 'Unknown User', 20) }}</h3>
                                    <p class="text-gray-400 text-sm mt-1">{{ \Str::limit($activity->description ?: ucfirst(str_replace('_', ' ', $activity->action)), 30) }}</p>
                                </div>
                            </div>
                            
                            <span class="px-2 py-1 bg-blue-500/20 text-blue-400 text-xs rounded-full border border-blue-500/30">
                                {{ \Str::limit(ucfirst(str_replace('_', ' ', $activity->action)), 12) }}
                            </span>
                        </div>
                        
                        <!-- Activity Info Grid -->
                        <div class="space-y-3">
                            <div class="flex items-center text-sm text-gray-300">
                                <i class="fas fa-clock w-4 mr-2 text-gray-400"></i>
                                <span>{{ $activity->created_at->format('M d, Y') }} - {{ $activity->created_at->format('g:i A') }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-300">
                                <i class="fas fa-map-marker-alt w-4 mr-2 text-gray-400"></i>
                                <span class="truncate">{{ $activity->ip_address }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="p-4 border-t border-gray-700">
                {{ $activities->appends(request()->query())->links() }}
            </div>
        @else
            <div class="admin-empty-state p-12">
                <i class="fas fa-history admin-empty-icon"></i>
                <div class="admin-empty-title">No Activities Found</div>
                <div class="admin-empty-description">
                    @if(request()->hasAny(['action', 'date_from', 'date_to']))
                        No activities match your current filters
                    @else
                        No activities have been recorded yet
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
