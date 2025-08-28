<section class="why-choose-section" style="background-image: url('/assets/images/home/why-choose-us/why-choose-us-background.png')">
    <div class="why-choose-container">
        <!-- Image section -->
        <div class="why-choose-image" data-aos="fade-right" data-aos-duration="1000" data-aos-offset="200">
            <img 
           src="{{asset('assets/images/home/why-choose-us/why-choose-us-text.png')}}" 
            alt="Why Choose Us"
            >
        </div>
        
        <!-- Stats section -->
        <div class="why-choose-stats">
            <div class="stat-circle" data-aos="fade-up" data-aos-delay="100">
                <span class="stat-circle-number stat-counter" data-target="{{ preg_replace('/[^0-9]/', '', $settings['stats_years'] ?? '20') }}" data-prefix="+">0</span>
                <span class="stat-circle-label">{{ $settings['stats_years_label'] ?? 'Year' }}</span>
            </div>
            <div class="stat-circle" data-aos="fade-up" data-aos-delay="200">
                <span class="stat-circle-number stat-counter" data-target="{{ preg_replace('/[^0-9]/', '', $settings['stats_projects'] ?? '60') }}" data-prefix="+">0</span>
                <span class="stat-circle-label">{{ $settings['stats_projects_label'] ?? 'Project' }}</span>
            </div>
            <div class="stat-circle" data-aos="fade-up" data-aos-delay="300">
                <span class="stat-circle-number stat-counter" data-target="{{ preg_replace('/[^0-9]/', '', $settings['stats_clients'] ?? '1200') }}" data-prefix="+">0</span>
                <span class="stat-circle-label">{{ $settings['stats_clients_label'] ?? 'Unit' }}</span>
            </div>
        </div>
    </div>
</section>