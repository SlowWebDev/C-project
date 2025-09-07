@extends('layouts.app')

@section('title', $project->title)
@section('description', $project->short_description)

@section('content')

<!-- Hero Section   -->
<section class="prj-hero">
    <!-- Background -->
    <img src="{{ Storage::url($project->image) }}" 
         alt="{{ $project->title }}" 
         class="prj-hero-bg"
         fetchpriority="high">
    <div class="prj-hero-overlay"></div>

    <!-- Content -->
    <div class="prj-hero-content">
        <!-- Title + Meta -->
        <div class="text-center md:text-left md:flex-1 max-w-2xl">
            <h1 class="prj-hero-title">{{ $project->title }}</h1>
            <div class="prj-hero-meta">
                <span class="prj-hero-meta-item">
                    <i class="fas fa-map-marker-alt prj-hero-icon"></i>
                    {{ $project->address }}
                </span>
                <span class="prj-hero-meta-item">
                    <i class="fas fa-building prj-hero-icon"></i>
                    {{ ucfirst($project->category) }}
                </span>
            </div>
        </div>

        <!-- Contact Form -->
        <form action="{{ route('project.inquiry', $project->id) }}" method="POST" class="prj-form">
            @csrf
            <input type="hidden" name="type" value="project_inquiry">
            <input type="hidden" name="project_id" value="{{ $project->id }}">
            
            <div class="space-y-5">
                <div class="space-y-4">
                    <div>
                        <label class="prj-form-label">First Name</label>
                        <input type="text" name="first_name" placeholder="First Name" class="prj-form-input" required value="{{ old('first_name') }}">
                        @error('first_name')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="prj-form-label">Last Name</label>
                        <input type="text" name="last_name" placeholder="Last Name" class="prj-form-input" required value="{{ old('last_name') }}">
                        @error('last_name')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="prj-form-label">Email</label>
                        <input type="email" name="email" placeholder="Email" class="prj-form-input" required value="{{ old('email') }}">
                        @error('email')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="prj-form-label">Phone Number</label>
                        <div class="relative">
                            <input type="tel" name="phone" pattern="[0-9]*" inputmode="numeric"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                   class="phone-input prj-form-input"
                                   placeholder="Phone number" required value="{{ old('phone') }}">
                        </div>
                        @error('phone')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="prj-form-label">Project</label>
                        <select class="prj-form-select" disabled>
                            <option value="{{ $project->id }}">{{ $project->title }}</option>
                        </select>
                        <p class="text-sm text-gray-400 mt-1">Inquiry for: {{ $project->title }}</p>
                    </div>
                </div>

                <button type="submit" class="prj-form-btn">Submit Inquiry</button>
            </div>
        </form>
    </div>
</section>


<!--  About Section  -->
<section class="prj-about">
    <div class="prj-section-box">
        <div class="prj-section-head">
            <h2 class="prj-section-title text-gray-900">About</h2>
            <div class="prj-section-line"></div>
        </div>
        <div class="prj-section-text">
            {!! nl2br(e($project->description)) !!}
        </div>
    </div>
</section>


<!--  Facilities Section  -->
@if(!empty($project->facilities))
<section class="prj-facilities" 
         style="background-image: url('/assets/images/home/why-choose-us/why-choose-us-background.png');">
    <div class="prj-section-box">
        <h2 class="prj-fac-title">Facilities</h2>
        <div class="prj-fac-line"></div>

        <div class="prj-fac-grid">
            @foreach($project->facilities as $facilityName)
                @php
                    $facilityData = collect(\App\Models\Project::getAvailableFacilities())
                        ->firstWhere('name', $facilityName);
                @endphp
                @if($facilityData)
                <div class="prj-fac-item">
                    <div class="prj-fac-icon">
                        <i class="fas {{ $facilityData['icon'] }}"></i>
                    </div>
                    <span class="prj-fac-name">{{ $facilityData['name'] }}</span>
                </div>
                @endif
            @endforeach
        </div>
    </div>
</section>
@endif


<!--  Gallery Section  -->
@if($project->gallery && count($project->gallery) > 0)
<section class="prj-gallery">
    <div class="prj-section-box">
        <div class="prj-section-head">
            <h2 class="prj-section-title text-gray-900">Gallery</h2>
            <div class="prj-section-line"></div>
        </div>
        
        <div id="gallery-grid" class="prj-gallery-grid">
            @foreach($project->gallery as $key => $image)
            <div class="prj-gallery-item group"
                 onclick="openGalleryModal('{{ Storage::url($image) }}', {{ $key }})">
                <img src="{{ Storage::url($image) }}" 
                     alt="{{ $project->title }} gallery image"
                     loading="lazy">
                <div class="prj-gallery-overlay"></div>
                <div class="prj-gallery-zoom">
                    <span class="prj-gallery-zoom-icon">
                        <i class="fas fa-expand text-gray-900 text-xl"></i>
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Gallery Modal -->
    <div id="gallery-modal" class="fixed inset-0 z-[60] hidden" role="dialog">
        <div class="absolute inset-0 bg-black/95 backdrop-blur-sm"></div>
        
        <!-- Close -->
        <button onclick="closeGalleryModal()" 
                class="absolute top-6 right-6 z-[70] w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-all duration-300">
            <i class="fas fa-times text-white text-xl"></i>
        </button>
        
        <!-- Nav -->
        <button id="prev-button" onclick="changeImage(-1)" 
                class="absolute left-4 top-1/2 -translate-y-1/2 z-[70] w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-all duration-300">
            <i class="fas fa-chevron-left text-white text-xl"></i>
        </button>
        <button id="next-button" onclick="changeImage(1)" 
                class="absolute right-4 top-1/2 -translate-y-1/2 z-[70] w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-all duration-300">
            <i class="fas fa-chevron-right text-white text-xl"></i>
        </button>

        <!-- Image -->
        <div class="fixed inset-0 z-[65] overflow-hidden">
            <div class="min-h-screen w-full flex items-center justify-center p-4">
                <img id="modal-image" src="" alt="Gallery image" 
                     class="max-w-[95vw] max-h-[90vh] object-contain select-none rounded-lg shadow-2xl transform transition-transform duration-500">
            </div>
        </div>
    </div>
</section>
@endif

@endsection
