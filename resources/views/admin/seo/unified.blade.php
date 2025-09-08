@extends('admin.layouts.admin')

@section('title', 'SEO Manager')
@section('description', 'Manage all SEO settings in one place.')

@section('content')
<div class="space-y-8">
    <!-- Page Header -->
    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">SEO Manager</h1>
            <p class="admin-page-description">Manage all your website SEO settings in one powerful interface.</p>
        </div>
        <div class="admin-flex-end">
            <form action="{{ route('admin.seo.reset') }}" method="POST" class="inline" 
                  onsubmit="return confirm('Are you sure you want to reset all SEO settings to defaults?')">
                @csrf
                <button type="submit" class="admin-btn-secondary">
                    <i class="fas fa-redo"></i>
                    Reset to Defaults
                </button>
            </form>
        </div>
    </div>
    <!-- Error Messages -->
    @if($errors->any())
        <div class="bg-red-900/20 backdrop-blur-sm border border-red-500/50 text-red-400 rounded-xl p-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-red-400 font-medium mb-2">There were errors:</h3>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li class="text-red-300">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Stats Overview -->
    <div class="admin-stats-grid">
    <div class="admin-stat-card">
        <div class="admin-stat-icon admin-stat-icon-blue">
            <i class="fas fa-file-alt"></i>
        </div>
        <div class="admin-stat-content">
            <div class="admin-stat-value">{{ count($pages) }}</div>
            <div class="admin-stat-label">Total Pages</div>
        </div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-icon admin-stat-icon-emerald">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="admin-stat-content">
            <div class="admin-stat-value">
                {{ collect($pages)->keys()->filter(function($pageKey) use ($seo) {
                    return $seo->getPageData($pageKey)['active'];
                })->count() }}
            </div>
            <div class="admin-stat-label">Active Pages</div>
        </div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-icon admin-stat-icon-purple">
            <i class="fas fa-search"></i>
        </div>
        <div class="admin-stat-content">
            <div class="admin-stat-value">
                {{ collect($pages)->keys()->filter(function($pageKey) use ($seo) {
                    return $seo->getPageData($pageKey)['indexable'];
                })->count() }}
            </div>
            <div class="admin-stat-label">Indexable Pages</div>
        </div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-icon admin-stat-icon-amber">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="admin-stat-content">
            <div class="admin-stat-value">{{ $seo->getSeoCompletionPercentage() }}%</div>
            <div class="admin-stat-label">SEO Completion</div>
        </div>
    </div>
</div>

    <!-- SEO Form -->
    <div class="admin-card">
        <form action="{{ route('admin.seo.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf

            <!-- Page Tabs -->
            <div class="border-b border-gray-700">
                <div class="flex flex-wrap gap-1 -mb-px">
                    @foreach($pages as $pageKey => $pageName)
                        <button type="button" 
                                class="page-tab px-4 py-3 text-sm font-medium rounded-t-lg border-b-2 transition-all duration-200 {{ $loop->first ? 'border-blue-500 bg-blue-600/10 text-blue-400' : 'border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-600' }}" 
                                onclick="showPage('{{ $pageKey }}')" 
                                id="tab-{{ $pageKey }}">
                            <i class="mr-2
                                @switch($pageKey)
                                    @case('home') fas fa-home @break
                                    @case('about') fas fa-info-circle @break
                                    @case('contact') fas fa-envelope @break
                                    @case('projects') fas fa-building @break
                                    @case('media') fas fa-images @break
                                    @case('careers') fas fa-briefcase @break
                                @endswitch
                            "></i>
                            {{ $pageName }}
                        </button>
                    @endforeach
                    <button type="button" 
                            class="page-tab px-4 py-3 text-sm font-medium rounded-t-lg border-b-2 border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-600 transition-all duration-200" 
                            onclick="showPage('global')" 
                            id="tab-global">
                        <i class="fas fa-globe mr-2"></i>
                        Global Settings
                    </button>
                </div>
            </div>

            <!-- Page Settings -->
            @foreach($pages as $pageKey => $pageName)
                <div class="page-content {{ $loop->first ? '' : 'hidden' }} py-6" id="page-{{ $pageKey }}">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <i class="
                                @switch($pageKey)
                                    @case('home') fas fa-home @break
                                    @case('about') fas fa-info-circle @break
                                    @case('contact') fas fa-envelope @break
                                    @case('projects') fas fa-building @break
                                    @case('media') fas fa-images @break
                                    @case('careers') fas fa-briefcase @break
                                @endswitch
                                text-blue-400 text-xl mr-3
                            "></i>
                            <h2 class="text-2xl font-bold text-white">{{ $pageName }} SEO Settings</h2>
                        </div>
                        <a href="{{ route('admin.seo.preview', $pageKey) }}" target="_blank" class="admin-btn-secondary text-sm">
                            <i class="fas fa-eye mr-2"></i>
                            Preview
                        </a>
                    </div>

                        <div class="admin-form-row-2">
                            <!-- Left Column -->
                            <div class="space-y-6">
                                <div class="admin-form-group">
                                    <label class="admin-label">
                                        Page Title
                                    </label>
                                    <input type="text" name="{{ $pageKey }}_title" 
                                           value="{{ old($pageKey . '_title', $seo->{$pageKey . '_title'}) }}" 
                                           class="admin-input" placeholder="Enter page title">
                                </div>

                                <div class="admin-form-group">
                                    <label class="admin-label">
                                        Meta Title
                                    </label>
                                    <input type="text" name="{{ $pageKey }}_meta_title" 
                                           value="{{ old($pageKey . '_meta_title', $seo->{$pageKey . '_meta_title'}) }}" 
                                           class="admin-input" placeholder="SEO title (max 70 characters)">
                                </div>

                                <div class="admin-form-group">
                                    <label class="admin-label">
                                        Meta Description
                                    </label>
                                    <textarea name="{{ $pageKey }}_meta_description" rows="3" 
                                              class="admin-textarea" placeholder="Brief description for search engines (150-160 characters)">{{ old($pageKey . '_meta_description', $seo->{$pageKey . '_meta_description'}) }}</textarea>
                                </div>

                                <div class="admin-form-group">
                                    <label class="admin-label">
                                        Meta Keywords
                                    </label>
                                    <input type="text" name="{{ $pageKey }}_meta_keywords" 
                                           value="{{ old($pageKey . '_meta_keywords', $seo->{$pageKey . '_meta_keywords'}) }}" 
                                           class="admin-input" placeholder="keyword1, keyword2, keyword3">
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="space-y-6">
                                <div class="admin-form-group">
                                    <label class="admin-label">
                                        Social Media Title
                                    </label>
                                    <input type="text" name="{{ $pageKey }}_og_title" 
                                           value="{{ old($pageKey . '_og_title', $seo->{$pageKey . '_og_title'}) }}" 
                                           class="admin-input" placeholder="Title for social media shares (max 60 characters)">
                                </div>

                                <div class="admin-form-group">
                                    <label class="admin-label">
                                        Social Media Description
                                    </label>
                                    <textarea name="{{ $pageKey }}_og_description" rows="3" 
                                              class="admin-textarea" placeholder="Description for social media shares (max 160 characters)">{{ old($pageKey . '_og_description', $seo->{$pageKey . '_og_description'}) }}</textarea>
                                </div>


                                <div class="admin-form-group">
                                    <label class="admin-label">
                                        Canonical URL
                                    </label>
                                    <input type="url" name="{{ $pageKey }}_canonical" 
                                           value="{{ old($pageKey . '_canonical', $seo->{$pageKey . '_canonical'}) }}" 
                                           class="admin-input" placeholder="https://example.com/page">
                                </div>
                            </div>
                        </div>

                    <!-- Settings Row -->
                    <div class="border-t border-gray-700 pt-6 mt-8">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="admin-form-group">
                                <label class="admin-label">
                                    Update Frequency
                                </label>
                                <select name="{{ $pageKey }}_frequency" class="admin-select">
                                    @foreach($frequencies as $freq => $label)
                                        <option value="{{ $freq }}" 
                                                {{ (old($pageKey . '_frequency', $seo->{$pageKey . '_frequency'} ?: 'monthly') == $freq) ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="admin-form-group">
                                <label class="admin-label">
                                    Status
                                </label>
                                <div class="mt-2">
                                    <label class="flex items-center space-x-3 p-3 bg-gray-700/30 rounded-lg border border-gray-600 hover:border-gray-500 cursor-pointer transition-colors">
                                        <input type="hidden" name="{{ $pageKey }}_active" value="0">
                                        <input type="checkbox" name="{{ $pageKey }}_active" value="1" 
                                               class="w-4 h-4 text-blue-600 bg-gray-700 border-gray-500 rounded focus:ring-blue-500" 
                                               {{ (old($pageKey . '_active', $seo->getPageData($pageKey)['active'])) ? 'checked' : '' }}>
                                        <span class="text-gray-300">Page is active</span>
                                    </label>
                                </div>
                            </div>

                            <div class="admin-form-group">
                                <label class="admin-label">
                                    Search Engine Visibility
                                </label>
                                <div class="mt-2">
                                    <label class="flex items-center space-x-3 p-3 bg-gray-700/30 rounded-lg border border-gray-600 hover:border-gray-500 cursor-pointer transition-colors">
                                        <input type="hidden" name="{{ $pageKey }}_indexable" value="0">
                                        <input type="checkbox" name="{{ $pageKey }}_indexable" value="1" 
                                               class="w-4 h-4 text-blue-600 bg-gray-700 border-gray-500 rounded focus:ring-blue-500" 
                                               {{ (old($pageKey . '_indexable', $seo->getPageData($pageKey)['indexable'])) ? 'checked' : '' }}>
                                        <span class="text-gray-300">Allow search engine indexing</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Global Settings -->
            <div class="page-content hidden py-6" id="page-global">
                <div class="flex items-center mb-6">
                    <h2 class="text-2xl font-bold text-white">Global SEO Settings</h2>
                </div>

                <div class="admin-form-row-2">
                    <div class="space-y-6">
                        <div class="admin-form-group">
                            <label class="admin-label">
                                Site Name
                            </label>
                            <input type="text" name="site_name" 
                                   value="{{ old('site_name', $seo->site_name) }}" 
                                   class="admin-input" placeholder="Enter site name">
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="admin-form-group">
                            <label class="admin-label">
                                Default Social Media Image
                            </label>
                            <input type="file" name="default_og_image" 
                                   accept="image/*" class="admin-input">
                            <p class="text-sm text-gray-400 mt-1">Fallback image for social media sharing (1200x630px recommended)</p>
                            @if($seo->default_og_image)
                                <div class="mt-2">
                                    <img src="{{ Storage::url($seo->default_og_image) }}" alt="Current default image" class="w-20 h-20 object-cover rounded border">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end pt-6 border-t border-gray-700">
                <button type="submit" class="admin-btn-primary text-lg px-8 py-4">
                    <i class="fas fa-save mr-2"></i>
                    Save All SEO Settings
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showPage(pageKey) {
    // Hide all page contents
    document.querySelectorAll('.page-content').forEach(el => {
        el.classList.add('hidden');
    });
    
    // Remove active styling from all tabs
    document.querySelectorAll('.page-tab').forEach(el => {
        el.classList.remove('border-blue-500', 'bg-blue-600/10', 'text-blue-400');
        el.classList.add('border-transparent', 'text-gray-400');
    });
    
    // Show selected page content
    document.getElementById('page-' + pageKey).classList.remove('hidden');
    
    // Add active styling to selected tab
    const activeTab = document.getElementById('tab-' + pageKey);
    activeTab.classList.remove('border-transparent', 'text-gray-400');
    activeTab.classList.add('border-blue-500', 'bg-blue-600/10', 'text-blue-400');
}

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const requiredFields = document.querySelectorAll('input[required], select[required]');
    let valid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.style.borderColor = '#ef4444';
            valid = false;
        } else {
            field.style.borderColor = '#e5e7eb';
        }
    });
    
    if (!valid) {
        e.preventDefault();
        alert('Please fill in all required fields.');
    }
});
</script>
@endsection
