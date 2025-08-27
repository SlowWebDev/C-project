<footer class="relative bg-center bg-cover bg-no-repeat min-h-[400px]" style="background-image: url('{{ asset('assets/images/footer.jpg') }}');">
            <!-- Overlay -->
    <div class="absolute inset-0 bg-white/30"></div>

    <!-- Container -->
    <div class="relative z-10 max-w-6xl mx-auto px-4 py-10">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Logo + Description -->
            <div class="lg:col-span-4">
        <img src="{{ \App\Models\Setting::getLogo('logo-footer') }}" class="site-logo" data-type="logo-footer" 
     alt="company Logo" 
     class="w-44 h-auto mb-4"
     loading="lazy"
     decoding="async"
     width="176"
     height="auto"
     data-logo-type="logo-footer">

                <p class="text-gray-600 text-sm leading-relaxed max-w-sm">
                    The company's extensive experience and focus on quality and innovation,
                    position it as a leading player in the development and investment sector.
                </p>
            </div>

            <!-- Quick Links -->
            <div class="footer-section">
                <h3 class="footer-heading">QUICK LINKS</h3>
                <ul class="footer-links">
                    <li><a href="{{ route('about') }}" class="footer-link">ABOUT US</a></li>
                    <li><a href="{{ route('media.index') }}" class="footer-link">MEDIA CENTER</a></li>
                    <li><a href="{{ route('careers.index') }}" class="footer-link">CAREERS</a></li>
                </ul>
            </div>

            <!-- Developments -->
            <div class="footer-section">
                <h3 class="footer-heading">DEVELOPMENTS</h3>
                <ul class="footer-links">
                    <li><a href="/" class="footer-link">RESIDENTIAL</a></li>
                    <li><a href="/" class="footer-link">COMMERCIAL</a></li>
                </ul>
            </div>

<!-- Scroll Up Button -->
<div class="lg:col-span-2 flex justify-start lg:justify-end">
    <button 
        onclick="window.scrollTo({top: 0, behavior: 'smooth'})"
        class="bg-orange-500 text-black px-6 py-3 rounded-md"
    >
        Scroll Up
    </button>
</div>

        </div>

        <!-- Social Media -->
        <div class="footer-social">
            <a href="/" class="footer-social-link"><i class="fab fa-facebook-f"></i></a>
            <a href="/" class="footer-social-link"><i class="fab fa-instagram"></i></a>
            <a href="/" class="footer-social-link"><i class="fab fa-linkedin-in"></i></a>
            <a href="/" class="footer-social-link"><i class="fab fa-tiktok"></i></a>
            <a href="/" class="footer-social-link"><i class="fab fa-whatsapp"></i></a>
        </div>

        <!-- Copyright -->
        <div class="text-center text-gray-600 border-t border-gray-300 pt-4 mt-8">
            All Copyrights for ©1SLOW
        </div>
    </div>
</footer>
