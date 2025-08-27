import './bootstrap';
import Swiper from 'swiper';
import { Autoplay, Pagination, Navigation } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/pagination';
import 'swiper/css/navigation';
import 'aos/dist/aos.css';
import './animations';
// Icons
import '@fortawesome/fontawesome-free/css/all.css';
import intlTelInput from 'intl-tel-input';
import 'intl-tel-input/build/css/intlTelInput.css';
import Alpine from 'alpinejs';

// Initialize Alpine.js
window.Alpine = Alpine;
Alpine.start();

// Mobile Menu
const menu = document.querySelector('.mobile-menu');
const overlay = document.querySelector('.mobile-menu-overlay');
const toggleBtn = document.querySelector('.mobile-menu-toggle');
const closeBtn = document.querySelector('.mobile-menu-close');


if (menu && toggleBtn) {
    let isOpen = false;

    const toggle = (show) => {
        if (isOpen === show) return;
        isOpen = show;
        document.body.style.overflow = show ? 'hidden' : '';
        menu.style.transform = `translateX(${show ? '0' : '100%'})`;
        if (overlay) {
            overlay.style.opacity = show ? '1' : '0';
            overlay.style.visibility = show ? 'visible' : 'hidden';
        }
    };

    toggleBtn.addEventListener('click', e => { e.stopPropagation(); toggle(true); });
    closeBtn?.addEventListener('click', e => { e.stopPropagation(); toggle(false); });
    overlay?.addEventListener('click', () => toggle(false));
    document.addEventListener('click', e => isOpen && !menu.contains(e.target) && toggle(false));
    window.addEventListener('keydown', e => e.key === 'Escape' && isOpen && toggle(false));
    window.addEventListener('resize', () => window.innerWidth >= 1024 && isOpen && toggle(false));
    menu.addEventListener('click', e => e.stopPropagation());
}


    // Hero Swiper

new Swiper('.hero-swiper', {
    modules: [Autoplay, Pagination],
    loop: true,
    speed: 800,
    autoplay: { delay: 4000, disableOnInteraction: false },
    pagination: { el: '.swiper-pagination', clickable: true },
    preloadImages: false,
    lazy: true,
});

    // Projects Swiper
new Swiper('.projects-swiper', {
    modules: [Navigation, Autoplay],
    loop: true,
    speed: 800,
    autoplay: { delay: 2000, disableOnInteraction: false, pauseOnMouseEnter: true },
    grabCursor: true,
    on: { init() { this.el.classList.add('group'); } },
    breakpoints: {
        640: { slidesPerView: 2 },
        1024: { slidesPerView: 3, spaceBetween: 32 },
        1280: { slidesPerView: 3, spaceBetween: 40 }
    }
});
// Partners Infinite Loop

new Swiper('.partners-swiper', {
    modules: [Autoplay],
    loop: true,
    centeredSlides: true,
    slidesPerView: 3,
    spaceBetween: 30,
    speed: 2000,
    autoplay: {
        delay: 0,
        disableOnInteraction: false
    },
    allowTouchMove: false,
    breakpoints: {
        320: { slidesPerView: 1 },
        768: { slidesPerView: 2 },
        1024: { slidesPerView: 3 }
    }
});
//  Media & News Swiper
new Swiper('.media-news-swiper', {
    modules: [Navigation],
    slidesPerView: 1,
    spaceBetween: 30,
    loop: true,
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    }
});

// Initialize phone inputs
document.addEventListener('DOMContentLoaded', function() {
    const phoneInputs = document.querySelectorAll('.phone-input');
    phoneInputs.forEach(phoneInput => {
        if (phoneInput) {
            const iti = intlTelInput(phoneInput, {
                initialCountry: 'eg',
                preferredCountries: ['eg', 'sa', 'ae', 'kw', 'qa', 'bh', 'om'],
                separateDialCode: true,
                utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js',
                customPlaceholder: function(selectedCountryPlaceholder, selectedCountryData) {
                    return 'Phone Number';
                },
            });

            // Style adjustments for the container
            const container = phoneInput.closest('.iti');
            if (container) {
                container.style.display = 'block';
                container.style.width = '100%';
            }
        }
    });
});



// Gallery Modal
const galleryModal = {
    modal: null,
    image: null,
    images: [],
    currentIndex: 0,
    
    init() {
        this.modal = document.getElementById('gallery-modal');
        this.image = document.getElementById('modal-image');
        
        // Event Listeners
        document.addEventListener('keydown', e => {
            if (!this.modal?.classList.contains('hidden')) {
                const keys = { 
                    'Escape': () => this.close(),
                    'ArrowLeft': () => this.navigate(-1),
                    'ArrowRight': () => this.navigate(1)
                };
                keys[e.key]?.();
            }
        });
    },

    open(src, index) {
        this.images = [...document.querySelectorAll('#gallery-grid img')].map(img => img.src);
        this.currentIndex = index;
        this.image.src = src;
        this.modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        // Preload adjacent images
        [-1, 1].forEach(offset => {
            const img = new Image();
            img.src = this.images[(index + offset + this.images.length) % this.images.length];
        });
    },

    close() {
        this.fadeTransition(() => {
            this.modal.classList.add('hidden');
            document.body.style.overflow = '';
        });
    },

    navigate(direction) {
        this.fadeTransition(() => {
            this.currentIndex = (this.currentIndex + direction + this.images.length) % this.images.length;
            this.image.src = this.images[this.currentIndex];
        });
    },

    fadeTransition(callback) {
        this.image.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            callback();
            this.image.classList.remove('scale-95', 'opacity-0');
        }, 200);
    }
};

// Initialize gallery modal
document.addEventListener('DOMContentLoaded', () => galleryModal.init());

// Make functions globally available
window.openGalleryModal = (src, index) => galleryModal.open(src, index);
window.closeGalleryModal = () => galleryModal.close();
window.changeImage = (direction) => galleryModal.navigate(direction);

