@props(['projects'])

<!-- Projects Sections -->
<div class="projects-wrapper">
    <!-- Commercial Projects -->
    <section class="py-16 md:py-20 bg-gray-50">
        <div class="container mx-auto px-4 mb-8 md:mb-12">
            <div class="section-title-wrapper flex items-center gap-4"
                 data-aos="fade-right"
                 data-aos-duration="600"
                 data-aos-offset="100"
                 data-aos-anchor-placement="top-bottom">
                <span class="h-[3px] w-12 bg-primary rounded-full"></span>
                <h2 class="section-title text-left">
                    Commercial Projects
                </h2>
            </div>
        </div>

        <div class="swiper projects-swiper projects-swiper--com overflow-visible max-w-[95vw] md:max-w-[1200px] lg:max-w-[1400px] mx-auto px-2 md:px-4"
             data-aos="fade-up"
             data-aos-duration="700"
             data-aos-offset="120"
             data-aos-anchor-placement="top-bottom"
             data-swiper-mobile-breakpoint="640">
            <div class="swiper-wrapper">
                @foreach($projects['commercial'] as $project)
                <div class="swiper-slide p-2">
                    <div class="project-card group">
                        <div class="project-image-wrapper">
                            <img src="{{ $project['image'] }}"
                                 alt="{{ $project['title'] }}"
                                 class="project-image w-full h-[250px] sm:h-[300px] md:h-[400px] object-cover transition-transform duration-300 group-hover:scale-105"
                                 loading="lazy"
                                 width="800"
                                 height="533">
                            <div class="project-overlay"></div>
                            <div class="project-location-badge">
                                <img src="{{ asset('assets/images/allprojects/projects/icons/lt.svg') }}" alt="Location" class="w-4 h-4">
                                <span>{{ $project['Addres'] }}</span>
                            </div>
                            <div class="project-content">
                                <h3 class="project-title">{{ $project['title'] }}</h3>
                                <p class="project-description">{{ $project['description'] }}</p>
                            </div>
                        </div>
                        <div class="p-4 flex justify-center">
                            <a href="{{ $project['link'] }}" class="explore-button">
                                <span>Explore</span>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Residential Projects -->
    <section class="py-16 md:py-20">
        <div class="container mx-auto px-4 mb-8 md:mb-12">
            <div class="section-title-wrapper flex items-center gap-4"
                 data-aos="fade-right"
                 data-aos-duration="600"
                 data-aos-offset="100"
                 data-aos-anchor-placement="top-bottom">
                <span class="h-[3px] w-12 bg-primary rounded-full"></span>
                <h2 class="section-title text-left">
                    Residential Projects
                </h2>
            </div>
        </div>

        <div class="swiper projects-swiper projects-swiper--res overflow-visible max-w-[95vw] md:max-w-[1200px] lg:max-w-[1400px] mx-auto px-2 md:px-4"
             data-aos="fade-up"
             data-aos-duration="700"
             data-aos-offset="140"
             data-aos-anchor-placement="top-bottom"
             data-swiper-mobile-breakpoint="640">
            <div class="swiper-wrapper">
                @foreach($projects['residential'] as $project)
                <div class="swiper-slide p-2">
                    <div class="project-card group">
                        <div class="project-image-wrapper">
                            <img src="{{ $project['image'] }}"
                                 alt="{{ $project['title'] }}"
                                 class="project-image w-full h-[250px] sm:h-[300px] md:h-[400px] object-cover transition-transform duration-300 group-hover:scale-105"
                                 loading="lazy"
                                 width="800"
                                 height="533">
                            <div class="project-overlay"></div>
                            <div class="project-location-badge">
                                <img src="{{ asset('assets/images/allprojects/projects/icons/lt.svg') }}" alt="Location" class="w-4 h-4">
                                <span>{{ $project['Addres'] }}</span>
                            </div>
                            <div class="project-content">
                                <h3 class="project-title">{{ $project['title'] }}</h3>
                                <p class="project-description">{{ $project['description'] }}</p>
                            </div>
                        </div>
                        <div class="p-4 flex justify-center">
                            <a href="{{ $project['link'] }}" class="explore-button">
                                <span>Explore</span>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
</div>