@extends('admin.layouts.admin')

@section('title', $media->title)
@section('description', $media->description)

@section('content')
<div class="admin-page-header">
    <div class="admin-flex-start">
        <a href="{{ route('admin.media.index') }}" class="admin-back-link mr-4">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="admin-page-title">{{ $media->title }}</h2>
            <p class="admin-page-description">View media details and information</p>
        </div>
    </div>
    <a href="{{ route('admin.media.edit', ['medium' => $media]) }}" 
       class="admin-btn-primary">
        <i class="fas fa-edit"></i>
        <span>Edit Media</span>
    </a>
</div>

<div class="admin-card">
    <!-- Hero Section -->
    <div class="admin-project-hero">
        <img src="{{ Storage::url($media->image) }}" 
             alt="{{ $media->title }}" 
             class="admin-hero-image">
        <div class="admin-hero-overlay">
            <div class="admin-hero-content">
                <div class="admin-hero-meta">
                    <span class="admin-category-badge media">
                        Media
                    </span>
                    <span class="admin-date-badge">
                        <i class="fas fa-calendar"></i>
                        {{ $media->created_at->format('M d, Y') }}
                    </span>
                </div>
                <h1 class="admin-hero-title">{{ $media->title }}</h1>
                <p class="admin-hero-description">{{ $media->description }}</p>
            </div>
        </div>
    </div>

    <!-- Media Details -->
    <div class="admin-show-content">
        <!-- Description Section -->
        <div class="admin-info-card">
            <h3 class="admin-section-title">
                <i class="fas fa-file-alt"></i>
                Media Description
            </h3>
            <div class="admin-description-content">
                {!! nl2br(e($media->description)) !!}
            </div>
        </div>

        <!-- Gallery Section -->
        @if($media->gallery && count($media->gallery) > 0)
        <div class="admin-info-card">
            <h3 class="admin-section-title">
                <i class="fas fa-images"></i>
                Media Gallery
            </h3>
            <div class="admin-gallery-grid">
                @foreach($media->gallery as $image)
                <div class="admin-gallery-item-view group">
                    <img src="{{ Storage::url($image) }}" 
                         alt="Gallery image" 
                         class="admin-gallery-image-view">
                    <div class="admin-gallery-overlay">
                        <a href="{{ Storage::url($image) }}" target="_blank" class="admin-gallery-link">
                            <i class="fas fa-search-plus"></i>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Status Section -->
        <div class="admin-status-section">
            <div class="admin-status-info">
                <span class="admin-status-indicator {{ $media->status === 'published' ? 'published' : 'draft' }}"></span>
                <span class="admin-status-text">Status: {{ ucfirst($media->status) }}</span>
            </div>
            <span class="admin-last-updated">
                Last updated: {{ $media->updated_at->diffForHumans() }}
            </span>
        </div>
    </div>
</div>
@endsection
