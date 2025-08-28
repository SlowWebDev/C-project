@extends('admin.layouts.admin')

@section('title', 'About Page Management')

@section('content')
<div class="admin-page-header">
    <div>
        <h1 class="admin-page-title">
            <i class="fas fa-info-circle mr-3"></i>
            About Page Management
        </h1>
        <p class="admin-page-description">
            Manage content for the about page including company description, mission and vision
        </p>
    </div>
    <div class="admin-flex-end">
        <a href="{{ route('about') }}" target="_blank" class="admin-btn-secondary">
            <i class="fas fa-external-link-alt"></i>
            Preview About Page
        </a>
    </div>
</div>

<form action="{{ route('admin.pages.about.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
    @csrf
    @method('PUT')

    <!-- Company Description Section -->
    <div class="admin-content-container">
        <div class="admin-section-title">
            <i class="fas fa-building text-blue-400"></i>
            Company Description
        </div>
        <p class="text-gray-400 text-sm mb-6">Main company description that appears at the top of about page (3 paragraphs)</p>
        
        <div class="space-y-6">
            <div class="admin-form-group">
                <label class="admin-label" for="description_paragraph_1">First Paragraph</label>
                <textarea 
                    id="description_paragraph_1" 
                    name="description_paragraph_1" 
                    class="admin-textarea @error('description_paragraph_1') error @enderror"
                    placeholder="Enter the first paragraph of company description..."
                    rows="4"
                >{{ old('description_paragraph_1', $aboutSettings['description_paragraph_1'] ?? '') }}</textarea>
                @error('description_paragraph_1')
                    <div class="admin-error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="admin-form-group">
                <label class="admin-label" for="description_paragraph_2">Second Paragraph</label>
                <textarea 
                    id="description_paragraph_2" 
                    name="description_paragraph_2" 
                    class="admin-textarea @error('description_paragraph_2') error @enderror"
                    placeholder="Enter the second paragraph of company description..."
                    rows="4"
                >{{ old('description_paragraph_2', $aboutSettings['description_paragraph_2'] ?? '') }}</textarea>
                @error('description_paragraph_2')
                    <div class="admin-error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="admin-form-group">
                <label class="admin-label" for="description_paragraph_3">Third Paragraph</label>
                <textarea 
                    id="description_paragraph_3" 
                    name="description_paragraph_3" 
                    class="admin-textarea @error('description_paragraph_3') error @enderror"
                    placeholder="Enter the third paragraph of company description..."
                    rows="4"
                >{{ old('description_paragraph_3', $aboutSettings['description_paragraph_3'] ?? '') }}</textarea>
                @error('description_paragraph_3')
                    <div class="admin-error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>
    </div>

    <!-- Mission Section -->
    <div class="admin-content-container">
        <div class="admin-section-title">
            <i class="fas fa-target text-emerald-400"></i>
            Mission Section
        </div>
        <p class="text-gray-400 text-sm mb-6">Company mission statement content</p>
        
        <div class="admin-form-group">
            <label class="admin-label" for="mission_content">Mission Content</label>
            <textarea 
                id="mission_content" 
                name="mission_content" 
                class="admin-textarea @error('mission_content') error @enderror"
                placeholder="Enter your company mission statement..."
                rows="6"
            >{{ old('mission_content', $aboutSettings['mission_content'] ?? '') }}</textarea>
            @error('mission_content')
                <div class="admin-error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <!-- Vision Section -->
    <div class="admin-content-container">
        <div class="admin-section-title">
            <i class="fas fa-eye text-purple-400"></i>
            Vision Section
        </div>
        <p class="text-gray-400 text-sm mb-6">Company vision statement content</p>
        
        <div class="admin-form-group">
            <label class="admin-label" for="vision_content">Vision Content</label>
            <textarea 
                id="vision_content" 
                name="vision_content" 
                class="admin-textarea @error('vision_content') error @enderror"
                placeholder="Enter your company vision statement..."
                rows="6"
            >{{ old('vision_content', $aboutSettings['vision_content'] ?? '') }}</textarea>
            @error('vision_content')
                <div class="admin-error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="admin-flex-end py-6">
        <div class="flex flex-col sm:flex-row gap-4">
            <button type="submit" class="admin-btn-primary px-8 py-3">
                <i class="fas fa-save mr-2"></i>
                Save About Page Settings
            </button>
            <button type="button" onclick="location.reload()" class="admin-btn-secondary px-8 py-3">
                <i class="fas fa-undo mr-2"></i>
                Reset Form
            </button>
        </div>
    </div>
</form>

@endsection
