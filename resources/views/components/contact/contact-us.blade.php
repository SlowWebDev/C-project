<section 
    class="relative py-20 md:py-28 bg-cover bg-center min-h-screen flex items-center overflow-hidden" 
    style="background-image: url('{{ asset('assets/images/home/why-choose-us/why-choose-us-background.png') }}')"
>
  <div class="container mx-auto px-4 relative z-10">
    <div class="max-w-6xl mx-auto text-center mb-16" data-aos="fade-down" data-aos-offset="0">
      <h1 class="text-4xl md:text-5xl font-bold text-orange-600 mb-4 drop-shadow-lg">Contact Us</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

      <!-- Contact Form -->
      <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-xl" data-aos="fade-right" data-aos-offset="0">
        <h2 class="text-2xl font-bold mb-6 text-gray-900">Send us a message</h2>
        <div class="space-y-5">
          @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-lg mb-4">
              <i class="fas fa-check-circle mr-2"></i>
              {{ session('success') }}
            </div>
          @endif

          @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-lg mb-4">
              <i class="fas fa-exclamation-circle mr-2"></i>
              {{ session('error') }}
            </div>
          @endif

          <form action="{{ route('contact.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="type" value="general">
            
            <div class="space-y-4">
              <!-- First & Last Name -->
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-1.5">First Name <span class="text-red-500">*</span></label>
                  <input type="text" name="first_name" placeholder="First Name" required value="{{ old('first_name') }}"
                         class="w-full h-12 px-4 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 text-sm placeholder-gray-400 focus:ring-1 focus:ring-orange-500 focus:border-orange-500">
                  @error('first_name')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                  @enderror
                </div>
                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-1.5">Last Name <span class="text-red-500">*</span></label>
                  <input type="text" name="last_name" placeholder="Last Name" required value="{{ old('last_name') }}"
                         class="w-full h-12 px-4 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 text-sm placeholder-gray-400 focus:ring-1 focus:ring-orange-500 focus:border-orange-500">
                  @error('last_name')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                  @enderror
                </div>
              </div>

              <!-- Email -->
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" placeholder="Email" required value="{{ old('email') }}"
                       class="w-full h-12 px-4 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 text-sm placeholder-gray-400 focus:ring-1 focus:ring-orange-500 focus:border-orange-500">
                @error('email')
                  <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
              </div>

              <!-- Phone Number -->
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Phone Number <span class="text-red-500">*</span></label>
                <input type="tel" name="phone" placeholder="0123456789" pattern="[0-9]*" inputmode="numeric" required
                       oninput="this.value = this.value.replace(/[^0-9]/g, '')" value="{{ old('phone') }}"
                       class="phone-input w-full h-12 px-4 bg-gray-50 border border-gray-200 rounded-lg text-black text-sm placeholder-gray-400 focus:ring-1 focus:ring-orange-500 focus:border-orange-500 focus:text-black">
                @error('phone')
                  <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
              </div>

              <!-- Message -->
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Message <span class="text-red-500">*</span></label>
                <textarea rows="5" name="message" placeholder="Your message" required
                          class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 text-sm placeholder-gray-400 focus:ring-1 focus:ring-orange-500 focus:border-orange-500">{{ old('message') }}</textarea>
                @error('message')
                  <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
              </div>
            </div>

            <!-- Submit Button -->
            <button type="submit"
                    class="w-full h-12 bg-orange-500 text-white rounded-lg font-medium text-base hover:bg-orange-600 transition-colors flex items-center justify-center">
              <i class="fas fa-paper-plane w-5 h-5 mr-2"></i> Send Message
            </button>
          </form>
        </div>
      </div>

      <!-- Contact Info & Map -->
      <div class="space-y-6" data-aos="fade-left" data-aos-offset="0">
        <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-2xl">
          <h2 class="text-2xl font-bold text-gray-800 mb-6">
            {{ isset($settings['contact_title']) && $settings['contact_title'] ? $settings['contact_title'] : 'Contact Information' }}
          </h2>

          <div class="space-y-6">
            @if(isset($settings['address_title'], $settings['address_content']) && $settings['address_content'])
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-4">
              <div class="flex-shrink-0 bg-orange-500/10 p-3 rounded-lg text-orange-500"><i class="fas fa-location-dot w-6 h-6"></i></div>
              <div>
                <h3 class="text-lg font-semibold text-gray-800">{{ $settings['address_title'] }}</h3>
                <p class="text-gray-600 mt-1">{!! nl2br(e($settings['address_content'])) !!}</p>
              </div>
            </div>
            @endif

            @if(isset($settings['email_title'], $settings['email_content']) && $settings['email_content'])
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-4">
              <div class="flex-shrink-0 bg-orange-500/10 p-3 rounded-lg text-orange-500"><i class="fas fa-envelope w-6 h-6"></i></div>
              <div>
                <h3 class="text-lg font-semibold text-gray-800">{{ $settings['email_title'] }}</h3>
                <a href="mailto:{{ $settings['email_content'] }}" class="text-gray-600 hover:text-orange-500 transition mt-1 block">{{ $settings['email_content'] }}</a>
              </div>
            </div>
            @endif

            @if(isset($settings['phone_title'], $settings['phone_content']) && $settings['phone_content'])
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-4">
              <div class="flex-shrink-0 bg-orange-500/10 p-3 rounded-lg text-orange-500"><i class="fas fa-phone w-6 h-6"></i></div>
              <div>
                <h3 class="text-lg font-semibold text-gray-800">{{ $settings['phone_title'] }}</h3>
                <a href="tel:{{ $settings['phone_content'] }}" class="text-gray-600 hover:text-orange-500 transition mt-1 block">{{ $settings['phone_content'] }}</a>
              </div>
            </div>
            @endif
          </div>
        </div>

        @if(isset($settings['map_embed_url']) && $settings['map_embed_url'])
        <div class="h-64 md:h-96 w-full rounded-2xl overflow-hidden shadow-2xl">
          <iframe class="w-full h-full" src="{{ $settings['map_embed_url'] }}" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
        @else
        <div class="h-64 md:h-96 w-full rounded-2xl overflow-hidden shadow-2xl bg-gray-100 flex items-center justify-center">
          <div class="text-center text-gray-500">
            <i class="fas fa-map-marker-alt text-4xl mb-4"></i>
            <p>Map will appear here when configured</p>
          </div>
        </div>
        @endif
      </div>

    </div>
  </div>
</section>
