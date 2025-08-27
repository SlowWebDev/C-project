@extends('admin.layouts.admin')

@section('title', 'Add New Media')
@section('description', 'Create a new media item.')

@section('content')
<div class="admin-page-header">
    <div class="admin-flex-start">
        <a href="{{ route('admin.media.index') }}" class="admin-back-link mr-4">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="admin-page-title">Add New Media</h2>
            <p class="admin-page-description">Upload and create a new media item</p>
        </div>
    </div>
</div>

<div class="admin-card admin-container-3xl">
    <form action="{{ route('admin.media.store') }}" method="POST" enctype="multipart/form-data" id="mediaForm" class="space-y-6">
        @csrf
        
        <div class="admin-form-row">
            <!-- Title -->
            <div class="admin-form-group">
                <label for="title" class="admin-label admin-label-required">Media Title</label>
                <input type="text" name="title" id="title" 
                       class="admin-input @error('title') error @enderror"
                       placeholder="Enter media title"
                       value="{{ old('title') }}" 
                       required>
                @error('title')
                    <div class="admin-error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Description -->
            <div class="admin-form-group">
                <label for="description" class="admin-label">Description</label>
                <textarea name="description" id="description" rows="4"
                          class="admin-input admin-textarea @error('description') error @enderror"
                          placeholder="Enter media description">{{ old('description') }}</textarea>
                @error('description')
                    <div class="admin-error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        <!-- Main Image -->
        <div class="admin-form-group">
            <label class="admin-label admin-label-required">Main Image</label>
            <div class="admin-file-upload">
                <div class="admin-upload-area">
                    <div class="admin-upload-content">
                        <i class="fas fa-cloud-upload-alt admin-upload-icon"></i>
                        <label for="image" class="admin-upload-label">
                            Upload Image or drag and drop
                            <input type="file" id="image" name="image" accept="image/*" class="sr-only" required data-max-size="2048">
                        </label>
                        <p class="admin-upload-hint">PNG, JPG, GIF up to 2MB</p>
                    </div>
                </div>
                <div id="main-image-preview" class="admin-image-preview hidden">
                    <div class="admin-preview-container">
                        <img src="#" alt="Preview" class="admin-preview-image">
                    </div>
                    <p class="admin-success-message">
                        <i class="fas fa-check-circle"></i>Main image selected
                    </p>
                </div>
                @error('image')
                    <div class="admin-error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        <!-- Gallery -->
        <div class="admin-form-group">
            <label class="admin-label">Gallery Images</label>
            <div class="admin-file-upload">
                <div class="admin-upload-area">
                    <div class="admin-upload-content">
                        <i class="fas fa-images admin-upload-icon"></i>
                        <label for="gallery" class="admin-upload-label">
                            Add Images or drag and drop
                            <input type="file" id="gallery" name="gallery[]" accept="image/*" class="sr-only" multiple data-max-size="2048">
                        </label>
                        <p class="admin-upload-hint">Upload up to 20 images (PNG, JPG, GIF up to 2MB each)</p>
                    </div>
                </div>
                <div id="gallery-preview" class="admin-gallery-preview"></div>
                <div id="gallery-count" class="admin-gallery-count hidden">
                    <i class="fas fa-images"></i><span id="gallery-count-text">0 images selected</span>
                </div>
                @error('gallery.*')
                    <div class="admin-error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        <!-- Status -->
        <div class="admin-form-group">
            <label for="status" class="admin-label admin-label-required">Status</label>
            <select name="status" id="status" 
                    class="admin-select @error('status') error @enderror"
                    required>
                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
            </select>
            <p class="admin-text-muted mt-2 text-xs">Draft items are only visible to administrators</p>
            @error('status')
                <div class="admin-error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <hr class="admin-divider">

        <!-- Actions -->
        <div class="admin-flex-end admin-gap-4">
            <a href="{{ route('admin.media.index') }}" 
               class="admin-btn-secondary">
                <i class="fas fa-arrow-left"></i>
                <span>Cancel</span>
            </a>
            <button type="submit" 
                    class="admin-btn-primary">
                <i class="fas fa-save"></i>
                <span>Create Media</span>
            </button>
        </div>
    </form>
</div>
@endsection


