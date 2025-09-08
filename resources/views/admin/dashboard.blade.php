@extends('admin.layouts.admin')

@section('title', 'Dashboard Overview')
@section('description', 'Welcome back overview of your website management system')

@section('content')
<div class="admin-page-header">
    <div>
        <h2 class="admin-page-title">Dashboard</h2>
        <p class="admin-page-description">Overview of your website management system</p>
    </div>
</div>


    <!-- Stats Overview -->
    <div class="admin-stats-grid">
        <div class="admin-stat-card">
            <div class="admin-stat-icon admin-stat-icon-blue">
                <i class="fas fa-building"></i>
            </div>
            <div class="admin-stat-content">
                <div class="admin-stat-value">{{ $projectsCount }}</div>
                <div class="admin-stat-label">Total Projects</div>
            </div>
        </div>

        <div class="admin-stat-card">
            <div class="admin-stat-icon admin-stat-icon-purple">
                <i class="fas fa-images"></i>
            </div>
            <div class="admin-stat-content">
                <div class="admin-stat-value">{{ $mediaCount }}</div>
                <div class="admin-stat-label">Media Items</div>
            </div>
        </div>

        <div class="admin-stat-card">
            <div class="admin-stat-icon admin-stat-icon-amber">
                <i class="fas fa-briefcase"></i>
            </div>
            <div class="admin-stat-content">
                <div class="admin-stat-value">{{ $jobsCount }}</div>
                <div class="admin-stat-label">Active Jobs</div>
            </div>
        </div>

        <div class="admin-stat-card">
            <div class="admin-stat-icon admin-stat-icon-emerald">
                <i class="fas fa-users"></i>
            </div>
            <div class="admin-stat-content">
                <div class="admin-stat-value">{{ $applicationsCount }}</div>
                <div class="admin-stat-label">Pending Applications</div>
            </div>
        </div>
    </div>

    <!-- Logo Management Section -->
    <div class="admin-card mb-8">
        <div class="admin-flex-between mb-6">
            <h2 class="text-xl font-semibold text-white">Website Logos</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Main Logo (White) -->
            <div class="bg-gray-700/50 rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-300 mb-4">Main Logo (White)</h3>
                <div class="bg-gray-900 rounded-lg p-4 admin-flex-center mb-4 h-20">
                    <img src="{{ \App\Models\Setting::getLogo('logo') }}" 
                         alt="Main Logo" 
                         class="max-h-12 w-auto">
                </div>
                <form action="{{ route('admin.settings.logo') }}" method="POST" enctype="multipart/form-data" class="logo-form">
                    @csrf
                    <input type="hidden" name="type" value="logo">
                    <label for="logoUpload-main" class="admin-btn-secondary w-full justify-center">
                        <i class="fas fa-upload"></i>
                        <span>Change Logo</span>
                    </label>
                    <input type="file" id="logoUpload-main" name="logo" class="hidden" accept="image/*" data-type="logo">
                </form>
            </div>

            <!-- Footer Logo (Dark) -->
            <div class="bg-gray-700/50 rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-300 mb-4">Footer Logo (Dark)</h3>
                <div class="bg-white rounded-lg p-4 admin-flex-center mb-4 h-20">
                    <img src="{{ \App\Models\Setting::getLogo('logo-footer') }}" 
                         alt="Footer Logo" 
                         class="max-h-12 w-auto">
                </div>
                <form action="{{ route('admin.settings.logo') }}" method="POST" enctype="multipart/form-data" class="logo-form">
                    @csrf
                    <input type="hidden" name="type" value="logo-footer">
                    <label for="logoUpload-footer" class="admin-btn-secondary w-full justify-center">
                        <i class="fas fa-upload"></i>
                        <span>Change Logo</span>
                    </label>
                    <input type="file" id="logoUpload-footer" name="logo" class="hidden" accept="image/*" data-type="logo-footer">
                </form>
            </div>
        </div>
    </div>

    <!-- Footer Settings Section -->
    <div class="admin-card mb-8">
        <div class="admin-flex-between mb-2">
            <h2 class="text-xl font-semibold text-white">Footer Settings</h2>
            <button onclick="toggleFooterSettings()" class="admin-btn-secondary" id="footer-toggle-btn">
                <i class="fas fa-chevron-down" id="footer-toggle-icon"></i>
                <span>Toggle Settings</span>
            </button>
        </div>
        
        <div id="footer-settings-content" class="hidden mt-6">
            <form action="{{ route('admin.settings.footer') }}" method="POST" class="space-y-6">
                @csrf
                
                <!-- Footer Content Section -->
                <div class="bg-gray-700/50 rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <i class="fas fa-align-left mr-2 text-blue-400"></i>
                            Footer Content
                        </h3>
                    </div>
                    <div class="space-y-4">
                        <!-- Footer Description -->
                        <div>
                            <label for="footer_description" class="block text-sm font-medium text-gray-300 mb-2">
                                Company Description
                            </label>
                            <textarea id="footer_description" name="footer_description" rows="3" 
                                      class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Company description text that appears in footer...">{{ \App\Models\Setting::get('footer_description', 'The company\'s extensive experience and focus on quality and innovation, position it as a leading player in the development and investment sector.') }}</textarea>
                        </div>
                        
                        <!-- Copyright Text -->
                        <div>
                            <label for="copyright_text" class="block text-sm font-medium text-gray-300 mb-2">
                                Copyright Text
                            </label>
                            <input type="text" id="copyright_text" name="copyright_text" 
                                   class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="All Copyrights for ©1SLOW"
                                   value="{{ \App\Models\Setting::get('copyright_text', 'All Copyrights for ©1SLOW') }}">
                        </div>
                    </div>
                </div>
                
                <!-- Social Media Section -->
                <div class="bg-gray-700/50 rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <i class="fas fa-share-alt mr-2 text-green-400"></i>
                            Social Media Links
                        </h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="facebook_url" class="block text-sm font-medium text-gray-300 mb-2">
                                <i class="fab fa-facebook-f text-blue-500 mr-2"></i>Facebook URL
                            </label>
                            <input type="url" id="facebook_url" name="facebook_url" 
                                   class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="https://facebook.com/yourpage"
                                   value="{{ \App\Models\Setting::get('social_facebook') }}">
                        </div>
                        
                        <div>
                            <label for="instagram_url" class="block text-sm font-medium text-gray-300 mb-2">
                                <i class="fab fa-instagram text-pink-500 mr-2"></i>Instagram URL
                            </label>
                            <input type="url" id="instagram_url" name="instagram_url" 
                                   class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="https://instagram.com/yourpage"
                                   value="{{ \App\Models\Setting::get('social_instagram') }}">
                        </div>
                        
                        <div>
                            <label for="linkedin_url" class="block text-sm font-medium text-gray-300 mb-2">
                                <i class="fab fa-linkedin-in text-blue-600 mr-2"></i>LinkedIn URL
                            </label>
                            <input type="url" id="linkedin_url" name="linkedin_url" 
                                   class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="https://linkedin.com/company/yourcompany"
                                   value="{{ \App\Models\Setting::get('social_linkedin') }}">
                        </div>
                        
                        <div>
                            <label for="tiktok_url" class="block text-sm font-medium text-gray-300 mb-2">
                                <i class="fab fa-tiktok text-gray-400 mr-2"></i>TikTok URL
                            </label>
                            <input type="url" id="tiktok_url" name="tiktok_url" 
                                   class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="https://tiktok.com/@yourpage"
                                   value="{{ \App\Models\Setting::get('social_tiktok') }}">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label for="whatsapp_url" class="block text-sm font-medium text-gray-300 mb-2">
                                <i class="fab fa-whatsapp text-green-500 mr-2"></i>WhatsApp URL
                            </label>
                            <input type="url" id="whatsapp_url" name="whatsapp_url" 
                                   class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="https://wa.me/yourphonenumber"
                                   value="{{ \App\Models\Setting::get('social_whatsapp') }}">
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex justify-end pt-4 border-t border-gray-600">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors flex items-center">
                        <i class="fas fa-save mr-2"></i>
                        Update Footer Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Projects -->
        <div class="admin-card">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-white">Recent Projects</h2>
                <a href="{{ route('admin.projects.index') }}" class="admin-btn-primary">
                    <i class="fas fa-list mr-2"></i>
                    <span>View All</span>
                </a>
            </div>

            <div class="space-y-4">
                @forelse($recentProjects as $project)
                    <div class="bg-gray-800 rounded-lg p-4">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0 h-12 w-12">
                                <img class="h-12 w-12 rounded-lg object-cover" 
                                     src="{{ Storage::url($project->image) }}" 
                                     alt="{{ $project->title }}">
                            </div>
                            <div class="flex-1">
                                <h3 class="font-medium text-white mb-1">{{ $project->title }}</h3>
                                <p class="text-sm text-gray-400">{{ Str::limit($project->description, 60) }}</p>
                                <div class="flex items-center mt-2">
                                    <span class="admin-badge {{ $project->status === 'published' ? 'admin-badge-success' : 'admin-badge-warning' }}">
                                        {{ ucfirst($project->status) }}
                                    </span>
                                    <span class="text-xs text-gray-500 ml-3">
                                        <i class="far fa-clock mr-1"></i>
                                        {{ $project->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                            <a href="{{ route('admin.projects.edit', $project) }}" 
                               class="admin-btn-secondary">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-folder-open text-3xl mb-3"></i>
                        <p>No projects found</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Applications -->
        <div class="admin-card">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-white">Recent Applications</h2>
                <a href="{{ route('admin.careers.index') }}" class="admin-btn-primary">
                    <i class="fas fa-users mr-2"></i>
                    <span>View All</span>
                </a>
            </div>

            <div class="space-y-4">
                @forelse($recentApplications as $application)
                    <div class="bg-gray-800 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-blue-600/20 flex items-center justify-center text-blue-400">
                                    <span class="text-lg font-medium">{{ strtoupper(substr($application->first_name, 0, 1) . substr($application->last_name, 0, 1)) }}</span>
                                </div>
                                <div>
                                    <h3 class="font-medium text-white">{{ $application->first_name }} {{ $application->last_name }}</h3>
                                    <p class="text-sm text-gray-400">{{ $application->job ? $application->job->title : 'Job Deleted' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-1 text-xs rounded-full
                                    @if($application->status === 'pending')
                                        bg-yellow-100 text-yellow-800
                                    @elseif($application->status === 'reviewed')
                                        bg-blue-100 text-blue-800
                                    @elseif($application->status === 'contacted')
                                        bg-green-100 text-green-800
                                    @else
                                        bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($application->status) }}
                                </span>
                                <a href="{{ route('admin.careers.index') }}" 
                                   class="admin-btn-secondary" title="View in Careers">
                                    <i class="fas fa-arrow-left"></i>
                                </a>
                            </div>
                        </div>
                        <div class="pl-13">
                            <span class="text-xs text-gray-500 mt-2 inline-block">
                                <i class="far fa-clock mr-1"></i>
                                Applied {{ $application->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-users text-3xl mb-3"></i>
                        <p>No applications found</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection