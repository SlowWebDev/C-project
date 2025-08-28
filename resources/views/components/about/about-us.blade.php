<!-- About us section -->
    <section class="overflow-hidden">
        <!-- Company Description Section -->
        <div class="bg-gray-50 py-16 lg:py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="max-w-4xl">
                    <div class="space-y-6">
                        @if(isset($settings['description_paragraph_1']) && $settings['description_paragraph_1'])
                        <p class="text-lg lg:text-xl text-gray-800 leading-relaxed" data-aos="fade-up" data-aos-duration="800" data-aos-offset="0">
                            {!! nl2br(e($settings['description_paragraph_1'])) !!}
                        </p>
                        @endif
                        
                        @if(isset($settings['description_paragraph_2']) && $settings['description_paragraph_2'])
                        <p class="text-lg lg:text-xl text-gray-800 leading-relaxed" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">
                            {!! nl2br(e($settings['description_paragraph_2'])) !!}
                        </p>
                        @endif
                        
                        @if(isset($settings['description_paragraph_3']) && $settings['description_paragraph_3'])
                        <p class="text-lg lg:text-xl text-gray-800 leading-relaxed" data-aos="fade-up" data-aos-duration="800" data-aos-delay="400">
                            {!! nl2br(e($settings['description_paragraph_3'])) !!}
                        </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if(isset($settings['mission_content']) && $settings['mission_content'])
        <!-- Mission Section -->
        <div class="relative py-20 lg:py-28 bg-center bg-cover bg-no-repeat overflow-hidden" style="background-image: url('{{ asset('assets/images/about/mission-bg.png') }}');" data-aos="fade-up" data-aos-duration="1000" data-aos-offset="0">
            <div class="absolute inset-0 bg-gray-900 bg-opacity-70"></div>
            
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">
                    <!-- Mission Title -->
                    <div class="order-1">
                        <h2 class="text-6xl sm:text-7xl lg:text-8xl xl:text-9xl font-bold text-white opacity-90 leading-none" data-aos="fade-right" data-aos-duration="1000">
                            Mission
                        </h2>
                    </div>
                    
                    <!-- Mission Content -->
                    <div class="text-white order-2" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="300">
                        <p class="text-lg lg:text-xl leading-relaxed">
                            {!! nl2br(e($settings['mission_content'])) !!}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(isset($settings['vision_content']) && $settings['vision_content'])
        <!-- Vision Section -->
        <div class="bg-gray-100 py-20 lg:py-28 overflow-hidden" data-aos="fade-up" data-aos-duration="1000" data-aos-offset="0">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">
                    <!-- Vision Content -->
                    <div class="text-gray-800 order-2 lg:order-1" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="300">
                        <p class="text-lg lg:text-xl leading-relaxed">
                            {!! nl2br(e($settings['vision_content'])) !!}
                        </p>
                    </div>
                    
                    <!-- Vision Title -->
                    <div class="order-1 lg:order-2 text-right">
                        <h2 class="text-6xl sm:text-7xl lg:text-8xl xl:text-9xl font-bold text-gray-800 opacity-90 leading-none" data-aos="fade-left" data-aos-duration="1000">
                            Vision
                        </h2>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </section>
