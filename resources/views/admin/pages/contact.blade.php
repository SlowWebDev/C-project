@extends('admin.layouts.admin')

@section('title', 'Contact Page Management')

@section('content')
<div class="admin-page-header">
    <div>
        <h1 class="admin-page-title">
            <i class="fas fa-envelope mr-3"></i>
            Contact Page Management
        </h1>
        <p class="admin-page-description">
            Manage contact information that appears on the contact page
        </p>
    </div>
    <div class="admin-flex-end">
        <a href="{{ route('contact.index') }}" target="_blank" class="admin-btn-secondary">
            <i class="fas fa-external-link-alt"></i>
            Preview Contact Page
        </a>
    </div>
</div>

<form action="{{ route('admin.pages.contact.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
    @csrf
    @method('PUT')

    <!-- Contact Information Section -->
    <div class="admin-content-container">
        <div class="admin-section-title">
            <i class="fas fa-address-book text-blue-400"></i>
            Contact Information
        </div>
        <p class="text-gray-400 text-sm mb-6">Contact details that will be displayed on the contact page</p>
        
        <div class="admin-form-group">
            <label class="admin-label" for="contact_title">Contact Information Title</label>
            <input 
                type="text" 
                id="contact_title" 
                name="contact_title" 
                value="{{ old('contact_title', $contactSettings['contact_title']) }}"
                class="admin-input @error('contact_title') error @enderror"
                placeholder="Enter contact section title (e.g., Contact Information)"
            >
            @error('contact_title')
                <div class="admin-error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>
        
        <div class="admin-form-row-2">
            <div class="admin-form-group">
                <label class="admin-label" for="address_title">Address Title</label>
                <input 
                    type="text" 
                    id="address_title" 
                    name="address_title" 
                    value="{{ old('address_title', $contactSettings['address_title']) }}"
                    class="admin-input @error('address_title') error @enderror"
                    placeholder="Enter address title (e.g., Our Address)"
                >
                @error('address_title')
                    <div class="admin-error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="admin-form-group">
                <label class="admin-label" for="address_content">Address Content</label>
                <textarea 
                    id="address_content" 
                    name="address_content" 
                    class="admin-textarea @error('address_content') error @enderror"
                    placeholder="Enter your company address"
                    rows="3"
                >{{ old('address_content', $contactSettings['address_content']) }}</textarea>
                @error('address_content')
                    <div class="admin-error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        <div class="admin-form-row-2">
            <div class="admin-form-group">
                <label class="admin-label" for="email_title">Email Title</label>
                <input 
                    type="text" 
                    id="email_title" 
                    name="email_title" 
                    value="{{ old('email_title', $contactSettings['email_title']) }}"
                    class="admin-input @error('email_title') error @enderror"
                    placeholder="Enter email title (e.g., Email Us)"
                >
                @error('email_title')
                    <div class="admin-error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="admin-form-group">
                <label class="admin-label" for="email_content">Email Address</label>
                <input 
                    type="email" 
                    id="email_content" 
                    name="email_content" 
                    value="{{ old('email_content', $contactSettings['email_content']) }}"
                    class="admin-input @error('email_content') error @enderror"
                    placeholder="Enter email address (e.g., info@company.com)"
                >
                @error('email_content')
                    <div class="admin-error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        <div class="admin-form-row-2">
            <div class="admin-form-group">
                <label class="admin-label" for="phone_title">Phone Title</label>
                <input 
                    type="text" 
                    id="phone_title" 
                    name="phone_title" 
                    value="{{ old('phone_title', $contactSettings['phone_title']) }}"
                    class="admin-input @error('phone_title') error @enderror"
                    placeholder="Enter phone title (e.g., Call Us)"
                >
                @error('phone_title')
                    <div class="admin-error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="admin-form-group">
                <label class="admin-label" for="phone_content">Phone Number</label>
                <input 
                    type="text" 
                    id="phone_content" 
                    name="phone_content" 
                    value="{{ old('phone_content', $contactSettings['phone_content']) }}"
                    class="admin-input @error('phone_content') error @enderror"
                    placeholder="Enter phone number (e.g., +20 123 456 7890)"
                >
                @error('phone_content')
                    <div class="admin-error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        <div class="admin-form-group">
            <label class="admin-label" for="map_embed_url">Google Maps Embed URL</label>
            <input 
                type="url" 
                id="map_embed_url" 
                name="map_embed_url" 
                value="{{ old('map_embed_url', $contactSettings['map_embed_url']) }}"
                class="admin-input @error('map_embed_url') error @enderror"
                placeholder="Enter Google Maps embed URL (optional)"
            >
            <p class="text-gray-400 text-sm mt-1">
                Go to Google Maps → Find your location → Share → Embed a map → Copy the src URL from the iframe
            </p>
            @error('map_embed_url')
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
                Save Contact Settings
            </button>
            <button type="button" onclick="location.reload()" class="admin-btn-secondary px-8 py-3">
                <i class="fas fa-undo mr-2"></i>
                Reset Form
            </button>
        </div>
    </div>
</form>

@endsection
