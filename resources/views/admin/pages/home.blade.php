{{--
    Admin Home Page Management
    
    Comprehensive homepage content editor including hero slider,
    PDF downloads, statistics, and CEO message sections.
    
    Author: SlowWebDev
--}}

@extends('admin.layouts.admin')

@section('title', 'Home Page Management')

@section('content')
<div class="admin-page-header">
    <div>
        <h1 class="admin-page-title">
            <i class="fas fa-home mr-3"></i>
            Home Page Management
        </h1>
        <p class="admin-page-description">
            Manage hero slider images, download button, statistics, and CEO message content for the homepage
        </p>
    </div>
    <div class="admin-flex-end">
        <a href="{{ route('home') }}" target="_blank" class="admin-btn-secondary">
            <i class="fas fa-external-link-alt"></i>
            Preview Home Page
        </a>
    </div>
</div>

<form action="{{ route('admin.pages.home.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
    @csrf
    @method('PUT')

    <!-- Hero Slider Images Section -->
    <div class="admin-content-container">
        <div class="admin-section-title">
            <i class="fas fa-images text-blue-400"></i>
            Hero Slider Images
        </div>
        <p class="text-gray-400 text-sm mb-6">Upload 3 slider images for the main hero section</p>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Slide 1 -->
            <div class="space-y-4">
                <h4 class="text-white font-medium flex items-center">
                    <i class="fas fa-image text-blue-400 mr-2"></i>
                    Slide 1
                </h4>
                <div class="admin-form-group">
                    <label class="admin-label" for="hero_slide_1">Hero Image 1</label>
                    <div class="admin-file-upload">
                        <div class="admin-upload-area" onclick="document.getElementById('hero_slide_1').click()">
                            <div class="admin-upload-content">
                                <i class="fas fa-cloud-upload-alt admin-upload-icon"></i>
                                <span class="admin-upload-label">Choose slide 1 image</span>
                                <p class="admin-upload-hint">JPG, PNG, GIF up to 5MB</p>
                            </div>
                        </div>
                        <input 
                            type="file" 
                            id="hero_slide_1" 
                            name="hero_slide_1" 
                            accept="image/*" 
                            class="hidden"
                            onchange="previewImage(this, 'slide1-preview')"
                        >
                        @if($homeSettings['hero_slide_1'])
                            <div class="admin-image-preview mt-4">
                                <div class="admin-preview-container">
                                    <img src="{{ asset('storage/' . $homeSettings['hero_slide_1']) }}" 
                                         alt="Current slide 1" 
                                         class="admin-preview-image">
                                    <span class="text-sm text-gray-400 mt-2 block">Current slide 1</span>
                                </div>
                            </div>
                        @endif
                        <div id="slide1-preview" class="admin-image-preview"></div>
                    </div>
                    @error('hero_slide_1')
                        <div class="admin-error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <!-- Slide 2 -->
            <div class="space-y-4">
                <h4 class="text-white font-medium flex items-center">
                    <i class="fas fa-image text-green-400 mr-2"></i>
                    Slide 2
                </h4>
                <div class="admin-form-group">
                    <label class="admin-label" for="hero_slide_2">Hero Image 2</label>
                    <div class="admin-file-upload">
                        <div class="admin-upload-area" onclick="document.getElementById('hero_slide_2').click()">
                            <div class="admin-upload-content">
                                <i class="fas fa-cloud-upload-alt admin-upload-icon"></i>
                                <span class="admin-upload-label">Choose slide 2 image</span>
                                <p class="admin-upload-hint">JPG, PNG, GIF up to 5MB</p>
                            </div>
                        </div>
                        <input 
                            type="file" 
                            id="hero_slide_2" 
                            name="hero_slide_2" 
                            accept="image/*" 
                            class="hidden"
                            onchange="previewImage(this, 'slide2-preview')"
                        >
                        @if($homeSettings['hero_slide_2'])
                            <div class="admin-image-preview mt-4">
                                <div class="admin-preview-container">
                                    <img src="{{ asset('storage/' . $homeSettings['hero_slide_2']) }}" 
                                         alt="Current slide 2" 
                                         class="admin-preview-image">
                                    <span class="text-sm text-gray-400 mt-2 block">Current slide 2</span>
                                </div>
                            </div>
                        @endif
                        <div id="slide2-preview" class="admin-image-preview"></div>
                    </div>
                    @error('hero_slide_2')
                        <div class="admin-error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <!-- Slide 3 -->
            <div class="space-y-4">
                <h4 class="text-white font-medium flex items-center">
                    <i class="fas fa-image text-orange-400 mr-2"></i>
                    Slide 3
                </h4>
                <div class="admin-form-group">
                    <label class="admin-label" for="hero_slide_3">Hero Image 3</label>
                    <div class="admin-file-upload">
                        <div class="admin-upload-area" onclick="document.getElementById('hero_slide_3').click()">
                            <div class="admin-upload-content">
                                <i class="fas fa-cloud-upload-alt admin-upload-icon"></i>
                                <span class="admin-upload-label">Choose slide 3 image</span>
                                <p class="admin-upload-hint">JPG, PNG, GIF up to 5MB</p>
                            </div>
                        </div>
                        <input 
                            type="file" 
                            id="hero_slide_3" 
                            name="hero_slide_3" 
                            accept="image/*" 
                            class="hidden"
                            onchange="previewImage(this, 'slide3-preview')"
                        >
                        @if($homeSettings['hero_slide_3'])
                            <div class="admin-image-preview mt-4">
                                <div class="admin-preview-container">
                                    <img src="{{ asset('storage/' . $homeSettings['hero_slide_3']) }}" 
                                         alt="Current slide 3" 
                                         class="admin-preview-image">
                                    <span class="text-sm text-gray-400 mt-2 block">Current slide 3</span>
                                </div>
                            </div>
                        @endif
                        <div id="slide3-preview" class="admin-image-preview"></div>
                    </div>
                    @error('hero_slide_3')
                        <div class="admin-error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- PDF Download Section -->
    <div class="admin-content-container">
        <div class="admin-section-title">
            <i class="fas fa-file-download text-red-400"></i>
            PDF Download Button
        </div>
        <p class="text-gray-400 text-sm mb-6">Configure the PDF download button that appears on hero slider</p>
        
        <div class="admin-form-row-2">
            <div class="admin-form-group">
                <label class="admin-label" for="hero_button_text">Button Text</label>
                <input 
                    type="text" 
                    id="hero_button_text" 
                    name="hero_button_text" 
                    value="{{ old('hero_button_text', $homeSettings['hero_button_text']) }}"
                    class="admin-input @error('hero_button_text') error @enderror"
                    placeholder="e.g., Download Brochure"
                >
                @error('hero_button_text')
                    <div class="admin-error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="admin-form-group">
                <label class="admin-label" for="hero_pdf">PDF File</label>
                <div class="admin-file-upload">
                    <div class="admin-upload-area" onclick="document.getElementById('hero_pdf').click()">
                        <div class="admin-upload-content">
                            <i class="fas fa-file-pdf admin-upload-icon text-red-400"></i>
                            <span class="admin-upload-label">Choose PDF file</span>
                            <p class="admin-upload-hint">PDF up to 10MB</p>
                        </div>
                    </div>
                    <input 
                        type="file" 
                        id="hero_pdf" 
                        name="hero_pdf" 
                        accept=".pdf" 
                        class="hidden"
                        onchange="previewFile(this, 'pdf-preview')"
                    >
                    @if($homeSettings['hero_pdf'])
                        <div class="mt-4 p-4 bg-gray-700 rounded-lg border border-gray-600">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-file-pdf text-red-400 text-2xl"></i>
                                <div>
                                    <p class="text-white font-medium">Current: {{ basename($homeSettings['hero_pdf']) }}</p>
                                    <a href="{{ asset('storage/' . $homeSettings['hero_pdf']) }}" target="_blank" 
                                       class="text-blue-400 hover:text-blue-300 text-sm">
                                        <i class="fas fa-external-link-alt mr-1"></i>
                                        View PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div id="pdf-preview"></div>
                </div>
                @error('hero_pdf')
                    <div class="admin-error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>
    </div>

    <!-- Statistics Section -->
    <div class="admin-content-container">
        <div class="admin-section-title">
            <i class="fas fa-chart-bar text-emerald-400"></i>
            Statistics Numbers
        </div>
        <p class="text-gray-400 text-sm mb-6">These numbers appear in animated circles in the "Why Choose Us" section</p>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Years of Experience -->
            <div class="space-y-4">
                <h4 class="text-white font-medium flex items-center">
                    <i class="fas fa-calendar-alt text-amber-400 mr-2"></i>
                    Years of Experience
                </h4>
                <div class="admin-form-group">
                    <label class="admin-label" for="stats_years">Years Number</label>
                    <input 
                        type="text" 
                        id="stats_years" 
                        name="stats_years" 
                        value="{{ old('stats_years', $homeSettings['stats_years']) }}"
                        class="admin-input @error('stats_years') error @enderror"
                        placeholder="e.g., 20"
                    >
                    @error('stats_years')
                        <div class="admin-error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="admin-form-group">
                    <label class="admin-label" for="stats_years_label">Years Label</label>
                    <input 
                        type="text" 
                        id="stats_years_label" 
                        name="stats_years_label" 
                        value="{{ old('stats_years_label', $homeSettings['stats_years_label']) }}"
                        class="admin-input @error('stats_years_label') error @enderror"
                        placeholder="e.g., Years"
                    >
                    @error('stats_years_label')
                        <div class="admin-error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <!-- Completed Projects -->
            <div class="space-y-4">
                <h4 class="text-white font-medium flex items-center">
                    <i class="fas fa-building text-blue-400 mr-2"></i>
                    Completed Projects
                </h4>
                <div class="admin-form-group">
                    <label class="admin-label" for="stats_projects">Projects Number</label>
                    <input 
                        type="text" 
                        id="stats_projects" 
                        name="stats_projects" 
                        value="{{ old('stats_projects', $homeSettings['stats_projects']) }}"
                        class="admin-input @error('stats_projects') error @enderror"
                        placeholder="e.g., 60"
                    >
                    @error('stats_projects')
                        <div class="admin-error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="admin-form-group">
                    <label class="admin-label" for="stats_projects_label">Projects Label</label>
                    <input 
                        type="text" 
                        id="stats_projects_label" 
                        name="stats_projects_label" 
                        value="{{ old('stats_projects_label', $homeSettings['stats_projects_label']) }}"
                        class="admin-input @error('stats_projects_label') error @enderror"
                        placeholder="e.g., Projects"
                    >
                    @error('stats_projects_label')
                        <div class="admin-error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <!-- Total Units -->
            <div class="space-y-4">
                <h4 class="text-white font-medium flex items-center">
                    <i class="fas fa-home text-emerald-400 mr-2"></i>
                    Total Units
                </h4>
                <div class="admin-form-group">
                    <label class="admin-label" for="stats_clients">Units Number</label>
                    <input 
                        type="text" 
                        id="stats_clients" 
                        name="stats_clients" 
                        value="{{ old('stats_clients', $homeSettings['stats_clients']) }}"
                        class="admin-input @error('stats_clients') error @enderror"
                        placeholder="e.g., 1200"
                    >
                    @error('stats_clients')
                        <div class="admin-error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="admin-form-group">
                    <label class="admin-label" for="stats_clients_label">Units Label</label>
                    <input 
                        type="text" 
                        id="stats_clients_label" 
                        name="stats_clients_label" 
                        value="{{ old('stats_clients_label', $homeSettings['stats_clients_label']) }}"
                        class="admin-input @error('stats_clients_label') error @enderror"
                        placeholder="e.g., Units"
                    >
                    @error('stats_clients_label')
                        <div class="admin-error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- CEO Message Section -->
    <div class="admin-content-container">
        <div class="admin-section-title">
            <i class="fas fa-user-tie text-indigo-400"></i>
            CEO Message
        </div>
        <p class="text-gray-400 text-sm mb-6">CEO message content that appears on the homepage</p>
        
        <div class="space-y-6">
            <div class="admin-form-row-2">
                <div class="admin-form-group">
                    <label class="admin-label" for="ceo_title">Section Title</label>
                    <input 
                        type="text" 
                        id="ceo_title" 
                        name="ceo_title" 
                        value="{{ old('ceo_title', $homeSettings['ceo_title']) }}"
                        class="admin-input @error('ceo_title') error @enderror"
                        placeholder="e.g., CEO Message"
                    >
                    @error('ceo_title')
                        <div class="admin-error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="admin-form-group">
                    <label class="admin-label" for="ceo_name">CEO Name</label>
                    <input 
                        type="text" 
                        id="ceo_name" 
                        name="ceo_name" 
                        value="{{ old('ceo_name', $homeSettings['ceo_name']) }}"
                        class="admin-input @error('ceo_name') error @enderror"
                        placeholder="e.g., Slow"
                    >
                    @error('ceo_name')
                        <div class="admin-error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="admin-form-group">
                <label class="admin-label" for="ceo_message">CEO Message Content</label>
                <textarea 
                    id="ceo_message" 
                    name="ceo_message" 
                    class="admin-textarea @error('ceo_message') error @enderror"
                    placeholder="Enter the CEO's message to website visitors"
                    rows="6"
                >{{ old('ceo_message', $homeSettings['ceo_message']) }}</textarea>
                @error('ceo_message')
                    <div class="admin-error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="admin-form-group">
                <label class="admin-label" for="ceo_image">CEO Photo</label>
                <div class="admin-file-upload">
                    <div class="admin-upload-area" onclick="document.getElementById('ceo_image').click()">
                        <div class="admin-upload-content">
                            <i class="fas fa-user-circle admin-upload-icon"></i>
                            <span class="admin-upload-label">Choose CEO photo</span>
                            <p class="admin-upload-hint">JPG, PNG, GIF up to 2MB</p>
                        </div>
                    </div>
                    <input 
                        type="file" 
                        id="ceo_image" 
                        name="ceo_image" 
                        accept="image/*" 
                        class="hidden"
                        onchange="previewImage(this, 'ceo-preview')"
                    >
                    @if($homeSettings['ceo_image'])
                        <div class="admin-image-preview mt-4">
                            <div class="admin-preview-container">
                                <img src="{{ asset('storage/' . $homeSettings['ceo_image']) }}" 
                                     alt="Current CEO image" 
                                     class="admin-preview-image">
                                <span class="text-sm text-gray-400 mt-2 block">Current CEO photo</span>
                            </div>
                        </div>
                    @endif
                    <div id="ceo-preview" class="admin-image-preview"></div>
                </div>
                @error('ceo_image')
                    <div class="admin-error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="admin-flex-end py-6">
        <div class="flex flex-col sm:flex-row gap-4">
            <button type="submit" class="admin-btn-primary px-8 py-3">
                <i class="fas fa-save mr-2"></i>
                Save Home Page Settings
            </button>
            <button type="button" onclick="location.reload()" class="admin-btn-secondary px-8 py-3">
                <i class="fas fa-undo mr-2"></i>
                Reset Form
            </button>
        </div>
    </div>
</form>

@endsection
