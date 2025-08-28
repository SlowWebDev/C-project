<section class="bg-white my-16">
  <div class="container mx-auto">
    <div class="grid md:grid-cols-2 gap-0 shadow-lg rounded-[50px] overflow-hidden bg-[#F1F1F1] md:min-h-[600px]">
      
      <!-- Text Side -->
      <div class="p-12 flex flex-col justify-center" data-aos="fade-right">
        <h2 class="text-[40px] font-bold mb-6">{{ $settings['ceo_title'] ?? 'CEO Message' }}</h2>
        <p class="text-gray-600 leading-relaxed">
          {{ $settings['ceo_message'] ?? 'At TG Developments, our journey has been shaped by over 20 years of dedicated service in the real estate industry. Throughout this time, we have grown and evolved, always with a focus on delivering high-quality, innovative projects that enhance the communities we touch.' }}
        </p>
        @if(isset($settings['ceo_name']) && $settings['ceo_name'])
        <div class="mt-6">
          <p class="font-bold text-lg">{{ $settings['ceo_name'] }}</p>
          <p class="text-gray-500">{{ $settings['ceo_position'] ?? 'CEO' }}</p>
        </div>
        @endif
      </div>

      <!-- Image Side -->
      <div class="min-h-[400px] md:min-h-[600px] h-full relative" data-aos="fade-left">
        @if(isset($settings['ceo_image']) && $settings['ceo_image'])
        <img 
          src="{{ asset('storage/' . $settings['ceo_image']) }}" 
          class="absolute inset-0 h-full w-full object-cover md:rounded-none rounded-b-[50px]"
          alt="{{ $settings['ceo_name'] ?? 'CEO' }} - IMG"
        >
        @else
        <img 
          src="{{asset('assets/images/home/ceo-message/ceo-img.jpeg')}}" 
          class="absolute inset-0 h-full w-full object-cover md:rounded-none rounded-b-[50px]"
          alt="CEO - IMG"
        >
        @endif
      </div>

    </div>
  </div>
</section>
