<!-- Media Gallery Component -->
<section class="py-16 md:py-20">
    <div class="container mx-auto px-4">
        <!-- Grid Layout -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 md:gap-8">
            @foreach($mediaItems as $item)
            <a href="{{ route('media.show', ['media' => $item->slug]) }}" 
               class="group block" 
               data-aos="fade-up" 
               data-aos-duration="600">
                <div class="relative overflow-hidden rounded-lg sm:rounded-xl shadow-md sm:shadow-lg aspect-[4/3] bg-gray-100">
                    <!-- Main Image -->
                    <img 
                        src="{{ Storage::url($item->image ?? ($item->gallery[0] ?? '')) }}"
                        alt="{{ $item->title }}"
                        class="w-full h-full object-cover transform transition-all duration-500 group-hover:scale-105"
                        loading="lazy"
                        width="800"
                        height="600"
                        onerror="this.parentElement.innerHTML = '<div class=\'w-full h-full flex items-center justify-center bg-gray-100\'><span class=\'text-gray-400\'><i class=\'fas fa-image text-4xl\'></i></span></div>'"
                    >
                    
                    <!-- Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/50 to-transparent transition-all duration-300">
                        <div class="absolute bottom-0 left-0 right-0 p-4">
                            <!-- Content for Both Mobile and Desktop -->
                            <div class="transform sm:translate-y-4 sm:group-hover:translate-y-0 transition-transform duration-300">
                                <h3 class="text-white text-xl sm:text-lg font-bold mb-2">
                                    {{ $item->title }}
                                </h3>
                                <p class="text-white/90 text-sm line-clamp-2">
                                    {{ $item->description }}
                                </p>
                                
                                <!-- Media Info (Desktop Only) -->
                                <div class="hidden sm:flex items-center space-x-4 mt-3">
                                    @if($item->gallery && count($item->gallery) > 0)
                                    <span class="flex items-center text-white/90 text-sm">
                                        <i class="fas fa-images mr-2"></i>
                                        {{ count($item->gallery) }}
                                    </span>
                                    @endif
                                    <span class="flex items-center text-white/90 text-sm">
                                        <i class="far fa-calendar mr-2"></i>
                                        {{ \Carbon\Carbon::parse($item->created_at)->format('M d, Y') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- View Button (Desktop Only) -->
                        <div class="hidden sm:block absolute top-4 right-4 transform translate-y-4 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300">
                            <span class="inline-flex items-center justify-center px-4 py-2 bg-white/90 hover:bg-white text-gray-900 rounded-full text-sm font-medium transition-colors duration-200">
                                View Details
                                <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-1 transition-transform duration-200"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>


    </div>
</section>

