@extends('admin.layouts.admin')

@section('title', 'Edit Job')
@section('description', 'Update job posting details')

@section('content')
<div class="admin-page-header">
    <div class="admin-flex-start">
        <a href="{{ route('admin.careers.index') }}" class="admin-back-link mr-4">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="admin-page-title">Edit Job</h2>
            <p class="admin-page-description">Update job posting details</p>
        </div>
    </div>
</div>

<div class="admin-form-container">
    <form action="{{ route('admin.careers.update', $job) }}" method="POST" class="space-y-6">
        @csrf
        @method('PATCH')
        
        <!-- Job Title -->
        <div class="admin-form-group">
            <label for="title" class="admin-label admin-label-required">Position Title</label>
            <input type="text" id="title" name="title" 
                value="{{ old('title', $job->title) }}"
                placeholder="Enter the position title"
                class="admin-input @error('title') error @enderror"
                required>
            @error('title')
                <div class="admin-error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Requirements -->
        <div class="admin-form-group">
            <label for="requirements" class="admin-label admin-label-required">
                Position Requirements
                <span class="block text-sm font-normal text-gray-400 mt-1">Enter each requirement on a new line</span>
            </label>
            <textarea id="requirements" name="requirements" rows="8"
                class="admin-input admin-textarea @error('requirements') error @enderror"
                placeholder="Enter job requirements (one per line)..."
                required>{{ old('requirements', implode("\n", $job->requirements)) }}</textarea>
            <p class="text-xs text-gray-400 mt-2">List the key qualifications, skills, and experience needed for this position.</p>
            @error('requirements')
                <div class="admin-error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <hr class="admin-divider">

        <div class="admin-flex-end admin-gap-4">
            <a href="{{ route('admin.careers.index') }}" 
               class="admin-btn-secondary">
                <i class="fas fa-arrow-left"></i>
                <span>Cancel</span>
            </a>
            <button type="submit" class="admin-btn-primary">
                <i class="fas fa-save"></i>
                <span>Update Job</span>
            </button>
        </div>
    </form>
</div>
@endsection
