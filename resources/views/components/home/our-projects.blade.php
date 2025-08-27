@props(['projects'])

<section class="py-24">
    <!-- Title -->
    <div class="container mx-auto px-4 mb-16">
        <h2 class="section-title" data-aos="fade-up" data-aos-duration="800">
            Our Projects
        </h2>
    </div>
    
    <!-- Swiper -->
    <div class="swiper projects-swiper overflow-visible max-w-[1400px] mx-auto px-4" 
         data-aos="fade-up" 
         data-aos-delay="100" 
         data-aos-duration="1000"
         data-swiper-autoplay="3000"
         data-swiper-loop="true"
         data-swiper-speed="1000"
         data-swiper-lazy="true"
         data-swiper-lazy-preloader-class="swiper-lazy-preloader"
         data-swiper-observer="true"
         data-swiper-observer-parents="true">
        
        <div class="swiper-wrapper">
            
            @foreach($projects as $project)
            <div class="swiper-slide p-2">
                <div class="project-card">
                    <div class="project-image-wrapper">
                        <img src="{{ Storage::url($project->image) }}" 
                             alt="{{ $project->title }}" 
                             class="project-image"
                             loading="lazy"
                             decoding="async"
                             width="400"
                             height="300"
                             sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 33vw">

                        <div class="project-overlay"></div>
                        <div class="project-location-badge">
                            <img src="{{ asset('assets/images/allprojects/projects/icons/lt.svg') }}" 
                                 alt="Location" 
                                 width="20" 
                                 height="20">
                            <span class="text-sm font-semibold">{{ $project->address }}</span>
                        </div>
                        <div class="project-content">
                            <h3 class="project-title">{{ $project->title }}</h3>
                            <p class="project-description">{{ $project->description }}</p>
                        </div>
                    </div>
                    <div class="p-4 flex justify-center">
                        <a href="{{ route('projects.show', $project->slug) }}" class="explore-button">
                            <span>Explore</span>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>