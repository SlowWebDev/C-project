/**
 * Main Application JavaScript - Frontend Core
 * 
 * Handles all frontend functionality including mobile menu, sliders,
 * phone inputs, and image gallery interactions.
 * 
 * @author SlowWebDev
 */

// ===== IMPORTS =====
import './bootstrap';
import Swiper from 'swiper';
import { Autoplay, Pagination, Navigation } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/pagination';
import 'swiper/css/navigation';
import 'aos/dist/aos.css';
import './animations';
import '@fortawesome/fontawesome-free/css/all.css';
import intlTelInput from 'intl-tel-input';
import 'intl-tel-input/build/css/intlTelInput.css';
import Alpine from 'alpinejs';

// ===== ALPINE.JS INITIALIZATION =====
window.Alpine = Alpine;
Alpine.start();

// ===== MOBILE MENU HANDLER =====
const menu = document.querySelector('.mobile-menu');
const overlay = document.querySelector('.mobile-menu-overlay');
const toggleBtn = document.querySelector('.mobile-menu-toggle');
const closeBtn = document.querySelector('.mobile-menu-close');
// Mobile menu toggle functionality
if (menu && toggleBtn) {
    let isOpen = false;

    // Toggle menu visibility with smooth animations
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

    // Event listeners for menu interactions
    toggleBtn.addEventListener('click', e => { e.stopPropagation(); toggle(true); });
    closeBtn?.addEventListener('click', e => { e.stopPropagation(); toggle(false); });
    overlay?.addEventListener('click', () => toggle(false));
    document.addEventListener('click', e => isOpen && !menu.contains(e.target) && toggle(false));
    window.addEventListener('keydown', e => e.key === 'Escape' && isOpen && toggle(false));
    window.addEventListener('resize', () => window.innerWidth >= 1024 && isOpen && toggle(false));
    menu.addEventListener('click', e => e.stopPropagation());
}
// ===== SWIPER SLIDERS INITIALIZATION =====

// Hero image slider with autoplay and pagination
new Swiper('.hero-swiper', {
    modules: [Autoplay, Pagination],
    loop: true,
    speed: 800,
    autoplay: { delay: 4000, disableOnInteraction: false },
    pagination: { el: '.swiper-pagination', clickable: true },
    preloadImages: false,
    lazy: true,
});

// Projects showcase slider with responsive breakpoints
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

// Partners continuous infinite loop slider
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

// Media and news slider with navigation arrows
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

// ===== INTERNATIONAL PHONE INPUT INITIALIZATION =====
document.addEventListener('DOMContentLoaded', function() {
    const phoneInputs = document.querySelectorAll('.phone-input');
    phoneInputs.forEach(phoneInput => {
        if (phoneInput) {
            // Initialize international phone input with MENA region focus
            const iti = intlTelInput(phoneInput, {
                initialCountry: 'eg',
                preferredCountries: ['eg', 'sa', 'ae', 'kw', 'qa', 'bh', 'om'],
                separateDialCode: true,
                utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js',
                customPlaceholder: function(selectedCountryPlaceholder, selectedCountryData) {
                    return 'Phone Number';
                },
            });

            // Apply responsive styling to phone input container
            const container = phoneInput.closest('.iti');
            if (container) {
                container.style.display = 'block';
                container.style.width = '100%';
            }
        }
    });
});

// ===== GALLERY MODAL SYSTEM =====
const galleryModal = {
    modal: null,
    image: null,
    images: [],
    currentIndex: 0,
    
    // Initialize modal with keyboard navigation
    init() {
        this.modal = document.getElementById('gallery-modal');
        this.image = document.getElementById('modal-image');
        
        // Setup keyboard navigation for modal
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

    // Open modal with specific image and preload adjacent images
    open(src, index) {
        this.images = [...document.querySelectorAll('#gallery-grid img')].map(img => img.src);
        this.currentIndex = index;
        this.image.src = src;
        this.modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        // Preload adjacent images for smooth navigation
        [-1, 1].forEach(offset => {
            const img = new Image();
            img.src = this.images[(index + offset + this.images.length) % this.images.length];
        });
    },

    // Close modal with fade transition
    close() {
        this.fadeTransition(() => {
            this.modal.classList.add('hidden');
            document.body.style.overflow = '';
        });
    },

    // Navigate between images with direction (-1 or 1)
    navigate(direction) {
        this.fadeTransition(() => {
            this.currentIndex = (this.currentIndex + direction + this.images.length) % this.images.length;
            this.image.src = this.images[this.currentIndex];
        });
    },

    // Smooth fade transition between images
    fadeTransition(callback) {
        this.image.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            callback();
            this.image.classList.remove('scale-95', 'opacity-0');
        }, 200);
    }
};

// Initialize gallery modal when DOM is ready
document.addEventListener('DOMContentLoaded', () => galleryModal.init());

// ===== GLOBAL FUNCTIONS FOR TEMPLATE USE =====
window.openGalleryModal = (src, index) => galleryModal.open(src, index);
window.closeGalleryModal = () => galleryModal.close();
window.changeImage = (direction) => galleryModal.navigate(direction);

