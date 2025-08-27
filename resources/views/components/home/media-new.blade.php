@props(['mediaItems'])

<section class="bg-white pt-6 md:pt-8 pb-12 md:pb-16 relative overflow-hidden" data-scroll>
    <div class="max-w-6xl mx-auto px-4">
        <!-- Section Title -->
        <div class="text-left mb-8" data-scroll data-scroll-offset="50">
            <h2 class="text-2xl md:text-3xl font-bold text-orange-500">Media & News</h2>
        </div>
        
        <!-- Arrows Container -->
        <div class="hidden md:flex absolute inset-0 z-10 items-center justify-between pointer-events-none">
            <div class="swiper-button-prev pointer-events-auto after:text-orange-500 after:text-4xl"></div>
            <div class="swiper-button-next pointer-events-auto after:text-orange-500 after:text-4xl"></div>
        </div>
        
        <!-- Slider Container -->
        <div class="relative" data-scroll data-scroll-offset="100">
            <div class="swiper media-news-swiper overflow-hidden">
                <div class="swiper-wrapper">
                    @foreach($mediaItems as $media)
                    <div class="swiper-slide">
                        <div class="mx-0 md:mx-2">
                            <div class="bg-[#090909] rounded-xl shadow-lg overflow-hidden group hover:shadow-xl transition-all duration-300">
                                <div class="flex flex-col md:grid md:grid-cols-2 min-h-[360px] md:min-h-[520px]">
                                    <!-- Image Section -->
                                    <div class="h-48 md:h-auto md:order-2">
                                        <img src="{{ Storage::url($media->image) }}"
                                             alt="{{ $media->title }}"
                                             class="w-full h-full object-cover object-center"
                                             loading="lazy"
                                             decoding="async"
                                             sizes="(max-width: 768px) 100vw, 50vw">
                                    </div>
                                    <!-- Content Section -->
                                    <div class="p-6 md:p-10 flex flex-col justify-center md:order-1">
                                        <div class="space-y-3">
                                            <h3 class="text-xl md:text-3xl font-bold text-white">
                                                {{ $media->title }}
                                            </h3>
                                            <p class="text-gray-300 text-sm md:text-lg">
                                                {{ $media->description }}
                                            </p>
                                            <div class="pt-3">
                                    <a href="{{ route('media.show', $media->slug) }}"
                                       class="inline-flex items-center justify-center px-5 py-2 md:px-6 md:py-3 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-md transition-colors text-sm md:text-lg">
                                        Learn More
                                    </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>