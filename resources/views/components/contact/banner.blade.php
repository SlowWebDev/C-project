   <section>
                <!-- Hero Section with Background Image -->
        <div class="relative min-h-[180px] sm:min-h-[200px] md:min-h-[240px] lg:min-h-[280px] bg-black overflow-hidden flex items-center">
            <!-- Background Image with Overlay -->
            <div class="absolute inset-0 w-full h-full">
                <img src="{{ asset('assets/images/contact/contact-banner.jpg') }}" 
                            alt="Our project Hero" 
                    class="w-full h-full object-cover opacity-60"
                    fetchpriority="high"
                    width="1920"
                    height="1080"
                >
                <!-- Dark overlay for better text readability -->
                <div class="absolute inset-0 bg-gradient-to-b from-black/70 via-black/50 to-black/70"></div>
            </div>
            
<!-- Content -->
<div class="relative z-10 container mx-auto px-4">
 <div class="max-w-4xl ml-12 sm:ml-16 md:ml-20 lg:ml-24 mt-24 sm:mt-28 md:mt-32 lg:mt-36">
                    <img src="{{ asset('assets/images/contact/contact-us.png') }}"
                            alt="our projects"
        class="w-auto max-w-[70px] sm:max-w-[85px] md:max-w-[100px] lg:max-w-[120px] drop-shadow-2xl">
 </div>
</section>