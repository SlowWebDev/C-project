@extends('admin.layouts.admin')

@section('title', 'Edit Project')
@section('description', 'Edit existing project.')

@section('content')
<div class="admin-page-header">
    <div class="admin-flex-start">
        <a href="{{ route('admin.projects.index') }}" class="admin-back-link mr-4">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="admin-page-title">Edit Project</h2>
            <p class="admin-page-description">Update project information and settings</p>
        </div>
    </div>
</div>

<div class="admin-card">
    <form action="{{ route('admin.projects.update', $project) }}" method="POST" enctype="multipart/form-data" id="projectForm" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="admin-form-row two-cols">
            <!-- Title -->
            <div class="admin-form-group">
                <label for="title" class="admin-label admin-label-required">Project Title</label>
                <input type="text" name="title" id="title" 
                       class="admin-input @error('title') error @enderror"
                       placeholder="Enter project title"
                       value="{{ old('title', $project->title) }}" 
                       required>
                @error('title')
                    <div class="admin-error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Category -->
            <div class="admin-form-group">
                <label for="category" class="admin-label admin-label-required">Category</label>
                <select name="category" id="category" 
                        class="admin-select @error('category') error @enderror"
                        required>
                    <option value="residential" {{ old('category', $project->category) == 'residential' ? 'selected' : '' }}>Residential</option>
                    <option value="commercial" {{ old('category', $project->category) == 'commercial' ? 'selected' : '' }}>Commercial</option>
                </select>
                @error('category')
                    <div class="admin-error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        <!-- Facilities -->
        <div class="admin-form-group">
            <label class="admin-label">Facilities</label>
            <div class="admin-checkbox-group">
                @foreach($availableFacilities as $facility)
                <label class="admin-checkbox-item">
                    <input type="checkbox" name="facilities[]" value="{{ $facility['name'] }}"
                           class="admin-checkbox"
                           {{ in_array($facility['name'], old('facilities', $project->facilities ?? [])) ? 'checked' : '' }}>
                    <div class="admin-checkbox-content">
                        <i class="fas {{ $facility['icon'] }}"></i>
                        <span>{{ $facility['name'] }}</span>
                    </div>
                </label>
                @endforeach
            </div>
            @error('facilities')
                <div class="admin-error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Short Description -->
        <div class="admin-form-group">
            <label for="short_description" class="admin-label admin-label-required">
                Short Description
                <span class="admin-text-muted">(max 150 characters)</span>
            </label>
            <textarea name="short_description" 
                      id="short_description" 
                      rows="2"
                      class="admin-textarea @error('short_description') error @enderror"
                      maxlength="150"
                      placeholder="Write a short description"
                      required>{{ old('short_description', $project->short_description) }}</textarea>
            <div class="admin-flex-between mt-2">
                <p class="admin-text-muted text-xs">Brief project summary</p>
                <span class="admin-text-muted text-xs" id="shortDescCounter">0/150</span>
            </div>
            @error('short_description')
                <div class="admin-error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Full Description -->
        <div class="admin-form-group">
            <label for="description" class="admin-label admin-label-required">Full Description</label>
            <textarea name="description" id="description" rows="5"
                      class="admin-textarea @error('description') error @enderror"
                      placeholder="Enter detailed description of the project..."
                      required>{{ old('description', $project->description) }}</textarea>
            <p class="admin-text-muted text-xs mt-2">Include key features, specifications, and important details.</p>
            @error('description')
                <div class="admin-error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Address -->
        <div class="admin-form-group">
            <label for="address" class="admin-label admin-label-required">Project Location</label>
            <input type="text" name="address" id="address"
                   class="admin-input @error('address') error @enderror"
                   placeholder="Enter project location"
                   value="{{ old('address', $project->address) }}" 
                   required>
            @error('address')
                <div class="admin-error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Main Image -->
        <div class="admin-form-group">
            <label class="admin-label">Main Project Image</label>
            <div class="admin-file-upload">
                <div class="admin-upload-area">
                    <div class="admin-upload-content">
                        <i class="fas fa-cloud-upload-alt admin-upload-icon"></i>
                        <label for="image" class="admin-upload-label">
                            Upload Image or drag and drop
                            <input type="file" id="image" name="image" accept="image/*" class="sr-only" data-max-size="2048">
                        </label>
                        <p class="admin-upload-hint">PNG, JPG, GIF up to 2MB</p>
                    </div>
                </div>
                <div id="main-image-preview" class="admin-image-preview {{ !$project->image ? 'hidden' : '' }}">
                    <div class="admin-preview-container">
                        <img src="{{ $project->image ? Storage::url($project->image) : '#' }}" 
                             alt="{{ $project->title }}" 
                             class="admin-preview-image">
                        @if($project->image)
                            <button type="button" 
                                    id="remove-main-image"
                                    class="admin-remove-btn">
                                ×
                            </button>
                        @endif
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
            <label class="admin-label">Project Gallery</label>
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
                <div id="gallery-preview" class="admin-gallery-preview">
                    @if($project->gallery && count($project->gallery) > 0)
                        @foreach($project->gallery as $index => $image)
                            <div class="admin-gallery-item" data-index="{{ $index }}">
                                <img src="{{ Storage::url($image) }}" 
                                     alt="Gallery image" 
                                     class="admin-gallery-image">
                                <button type="button" 
                                        class="admin-remove-btn remove-gallery-image"
                                        data-index="{{ $index }}">×</button>
                            </div>
                        @endforeach
                    @endif
                </div>
                <div id="gallery-count" class="admin-gallery-count {{ count($project->gallery ?? []) > 0 ? '' : 'hidden' }}">
                    <i class="fas fa-images"></i><span id="gallery-count-text">{{ count($project->gallery ?? []) }} images selected</span>
                </div>

                <!-- Hidden inputs for gallery management -->
                <input type="hidden" name="gallery_images" id="gallery_images" value="{{ implode(',', $project->gallery ?? []) }}">
                <input type="hidden" name="deleted_gallery_images" id="deleted_gallery_images" value="">
                
                @error('gallery')
                    <div class="admin-error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                @enderror
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
            <label for="status" class="admin-label admin-label-required">Project Status</label>
            <select name="status" id="status" 
                    class="admin-select @error('status') error @enderror"
                    required>
                <option value="draft" {{ old('status', $project->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="published" {{ old('status', $project->status) == 'published' ? 'selected' : '' }}>Published</option>
            </select>
            <p class="admin-text-muted text-xs mt-2">Draft projects are only visible to administrators</p>
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
            <a href="{{ route('admin.projects.index') }}" 
               class="admin-btn-secondary">
                <i class="fas fa-arrow-left"></i>
                <span>Cancel</span>
            </a>
            <button type="submit" 
                    class="admin-btn-primary">
                <i class="fas fa-save"></i>
                <span>Save Changes</span>
            </button>
        </div>
    </form>
</div>
@endsection
