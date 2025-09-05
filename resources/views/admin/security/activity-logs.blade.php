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
    </div>

    <!-- Filters -->
    <div class="admin-card">
        <form method="GET" class="admin-form-row grid-cols-1 md:grid-cols-4">
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

            <div class="admin-form-group">
                <label class="admin-label opacity-0">Actions</label>
                <div class="flex gap-2">
                    <button type="submit" class="admin-btn-primary">
                        <i class="fas fa-search"></i>Filter
                    </button>
                    <a href="{{ route('admin.security.activity-logs') }}" class="admin-btn-secondary">
                        <i class="fas fa-times"></i>Clear
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Activity Logs -->
    <div class="admin-content-container">
        <div class="admin-flex-between p-6 border-b border-gray-700">
            <h2 class="admin-section-title mb-0">
                <i class="fas fa-history text-blue-400"></i>
                Activity History
            </h2>
        </div>

        @if($activities->count() > 0)
            <div class="divide-y divide-gray-700">
                @foreach($activities as $activity)
                    <div class="p-4 hover:bg-gray-700/20 transition-colors">
                        <div class="flex items-start space-x-4">
                            <!-- User Avatar -->
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500/30 to-purple-500/30 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-user text-blue-400"></i>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <!-- Main Info -->
                                <div class="flex items-start justify-between mb-2">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-3 mb-1">
                                            <h3 class="text-white font-semibold text-lg">{{ $activity->user->name ?? 'Unknown User' }}</h3>
                                            @php
                                                $actionColors = [
                                                    'created' => 'green',
                                                    'updated' => 'blue', 
                                                    'deleted' => 'red',
                                                    'login' => 'purple',
                                                    'logout' => 'orange',
                                                    'password' => 'yellow',
                                                    'email' => 'cyan'
                                                ];
                                                $color = 'gray';
                                                foreach($actionColors as $action => $actionColor) {
                                                    if(str_contains(strtolower($activity->action), $action)) {
                                                        $color = $actionColor;
                                                        break;
                                                    }
                                                }
                                            @endphp
                                            <span class="px-3 py-1 bg-{{ $color }}-500/20 text-{{ $color }}-400 text-sm font-medium rounded-full border border-{{ $color }}-500/30">
                                                {{ ucfirst(str_replace('_', ' ', $activity->action)) }}
                                            </span>
                                        </div>
                                        <p class="text-gray-300 text-base leading-relaxed">
                                            {{ $activity->description ?: ucfirst(str_replace('_', ' ', $activity->action)) }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Meta Information -->
                                <div class="flex items-center gap-4 text-sm text-gray-400 mt-3">
                                    <div class="flex items-center">
                                        <i class="fas fa-clock mr-2 text-blue-400"></i>
                                        <span>{{ $activity->created_at->format('M d, Y') }}</span>
                                        <span class="mx-1">at</span>
                                        <span class="font-medium text-white">{{ $activity->created_at->format('g:i A') }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-map-marker-alt mr-2 text-green-400"></i>
                                        <span>{{ $activity->ip_address }}</span>
                                    </div>
                                    @if($activity->user_agent)
                                        <div class="flex items-center">
                                            <i class="fas fa-desktop mr-2 text-purple-400"></i>
                                            <span class="truncate max-w-xs" title="{{ $activity->user_agent }}">
                                                @php
                                                    $agent = new \Jenssegers\Agent\Agent();
                                                    $agent->setUserAgent($activity->user_agent);
                                                    echo $agent->browser() . ' on ' . $agent->platform();
                                                @endphp
                                            </span>
                                        </div>
                                    @endif
                                </div>

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
