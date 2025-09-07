@extends('admin.layouts.admin')

@section('title', $project->title)
@section('description', $project->short_description)

@section('content')
<div class="admin-page-header">
    <div class="admin-flex-start">
        <a href="{{ route('admin.projects.index') }}" class="admin-back-link mr-4">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="admin-page-title">{{ $project->title }}</h2>
            <p class="admin-page-description">{{ $project->short_description }}</p>
        </div>
    </div>
    <a href="{{ route('admin.projects.edit', $project) }}" class="admin-btn-primary">
        <i class="fas fa-edit"></i>
        <span>Edit Project</span>
    </a>
</div>

<div class="admin-card">
    <!-- Hero Section -->
    <div class="admin-project-hero">
        <img src="{{ Storage::url($project->image) }}" 
             alt="{{ $project->title }}" 
             class="admin-hero-image">
        <div class="admin-hero-overlay">
            <div class="admin-hero-content">
                <div class="admin-hero-meta">
                    <span class="admin-category-badge {{ $project->category === 'residential' ? 'residential' : 'commercial' }}">
                        {{ ucfirst($project->category) }}
                    </span>
                    <span class="admin-date-badge">
                        <i class="fas fa-calendar"></i>
                        {{ $project->created_at->format('M d, Y') }}
                    </span>
                </div>
                <h1 class="admin-hero-title">{{ $project->title }}</h1>
                <p class="admin-hero-description">{{ $project->short_description }}</p>
            </div>
        </div>
    </div>

    <!-- Project Details -->
    <div class="admin-show-content">
        <!-- Location Section -->
        <div class="admin-info-card">
            <h3 class="admin-section-title">
                <i class="fas fa-map-marker-alt"></i>
                Location
            </h3>
            <p class="admin-section-text">{{ $project->address }}</p>
        </div>

        <!-- Facilities Section -->
        @if($project->facilities && count($project->facilities) > 0)
        <div class="admin-info-card">
            <h3 class="admin-section-title">
                <i class="fas fa-star"></i>
                Facilities
            </h3>
            <div class="admin-facilities-grid">
                @foreach($project->facilities as $facilityName)
                    @php
                        $facilityData = collect(\App\Models\Project::getAvailableFacilities())
                            ->firstWhere('name', $facilityName);
                    @endphp
                    @if($facilityData)
                    <div class="admin-facility-item">
                        <i class="fas {{ $facilityData['icon'] }}"></i>
                        <span>{{ $facilityData['name'] }}</span>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
        @endif

        <!-- Description Section -->
        <div class="admin-info-card">
            <h3 class="admin-section-title">
                <i class="fas fa-file-alt"></i>
                Project Description
            </h3>
            <div class="admin-description-content">
                {!! nl2br(e($project->description)) !!}
            </div>
        </div>

        <!-- Gallery Section -->
        @if($project->gallery && count($project->gallery) > 0)
        <div class="admin-info-card">
            <h3 class="admin-section-title">
                <i class="fas fa-images"></i>
                Project Gallery
            </h3>
            <div class="admin-gallery-grid">
                @foreach($project->gallery as $image)
                <div class="admin-gallery-item-view group">
                    <img src="{{ Storage::url($image) }}" 
                         alt="Gallery image" 
                         class="admin-gallery-image-view">
                    <div class="admin-gallery-overlay">
                        <i class="fas fa-search-plus"></i>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Status Section -->
        <div class="admin-status-section">
            <div class="admin-status-info">
                <span class="admin-status-indicator {{ $project->status === 'published' ? 'published' : 'draft' }}"></span>
                <span class="admin-status-text">Status: {{ ucfirst($project->status) }}</span>
            </div>
            <span class="admin-last-updated">
                Last updated: {{ $project->updated_at->diffForHumans() }}
            </span>
        </div>
    </div>
</div>
@endsection
