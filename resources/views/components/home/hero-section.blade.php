{{--
    Home Hero Section Component - Main Landing Carousel
    
    Full-screen image slider with company slogan, brochure download,
    and responsive design for all device sizes.
    
    Author: SlowWebDev
--}}

<section class="relative h-screen overflow-hidden">
    <div class="swiper hero-swiper h-full">
        <div class="swiper-wrapper">
@php
$defaultSlides = ['1-img.jpg', 'img-2.jpeg', 'img-3.png'];
$adminSlides = [
    $settings['hero_slide_1'] ?? '',
    $settings['hero_slide_2'] ?? '', 
    $settings['hero_slide_3'] ?? ''
];
@endphp
@foreach($defaultSlides as $index => $slide)
    <div class="swiper-slide">
        <div class="relative h-full">
            <div class="absolute inset-0 bg-gradient-to-b from-black/50 via-transparent to-black/50 z-10"></div>

            @if(!empty($adminSlides[$index]))
                <img src="{{ asset('storage/' . $adminSlides[$index]) }}" 
                     alt="Hero slide {{ $index + 1 }}" 
                     class="absolute inset-0 w-full h-full object-cover"
                     width="1920" height="1080"
                     {{ $index === 0 ? 'fetchpriority=high' : 'loading=lazy' }}
                     decoding="async">
            @else
                <img src="{{ asset('assets/images/home/hero/' . $slide) }}" 
                     alt="Hero slide {{ $index + 1 }}" 
                     class="absolute inset-0 w-full h-full object-cover"
                     width="1920" height="1080"
                     {{ $index === 0 ? 'fetchpriority=high' : 'loading=lazy' }}
                     decoding="async">
            @endif

            {{-- Hero Content Container --}}
            <div class="absolute inset-x-0 top-[30%] mx-auto z-20 flex flex-col items-center justify-center space-y-8 px-4">
                {{-- Company Logo/Slogan --}}
                <img src="{{ asset('assets/images/home/hero/slogan.png') }}" 
                     alt="Slogan"
                     width="600" height="200"
                     {{ $index === 0 ? 'fetchpriority=high' : 'loading=lazy' }}
                     decoding="async"
                     class="h-[15vh] sm:h-[16vh] lg:h-[18vh] object-contain mx-auto transition-all duration-500">

                {{-- Brochure Download Button --}}
                @if(isset($settings['hero_pdf']) && $settings['hero_pdf'])
                    <a href="{{ asset('storage/' . $settings['hero_pdf']) }}" 
                       class="btn-hero" 
                       download
                       target="_blank">
                        {{ $settings['hero_button_text'] ?? 'Download Brochure' }}
                    </a>
                @else
                    <a href="#" class="btn-hero">
                        {{ $settings['hero_button_text'] ?? 'Download Brochure' }}
                    </a>
                @endif
            </div>
        </div>
    </div>
@endforeach

        </div>
        
        {{-- Slider Navigation Dots --}}
        <div class="swiper-pagination [&>span.swiper-pagination-bullet-active]:bg-[#ff6b00]">
           
</section>
