/**
 * Admin Panel JavaScript
 * 
 * @author SlowWebDev
 */

import Toastify from 'toastify-js';
import 'toastify-js/src/toastify.css';
import '@fortawesome/fontawesome-free/css/all.css';

/* ===== CONSTANTS ===== */
const CONFIG = {
    TOAST_DURATION: 3000,
    MAX_FILE_SIZE: 2 * 1024 * 1024, // 2MB
    ALLOWED_IMAGE_TYPES: ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
    API_ENDPOINTS: {
        UPLOAD_LOGO: '/admin/settings/logo'
    },
    SELECTORS: {
        DROPDOWN_TOGGLE: '.dropdown-toggle',
        FILE_INPUT: 'input[type="file"]',
        RICH_EDITOR: '.rich-editor',
        ADMIN_FILE_INPUT: '.admin-file-input'
    }
};

/* ===== TOAST NOTIFICATION SYSTEM ===== */
class ToastManager {
    static config = {
        duration: CONFIG.TOAST_DURATION,
        gravity: "top",
        position: "left",
        style: {
            borderRadius: "8px",
            fontFamily: "system-ui, -apple-system, sans-serif"
        }
    };

    static show(message, type = 'success') {
        const typeColors = {
            success: 'var(--admin-success)',
            error: 'var(--admin-danger)',
            warning: 'var(--admin-warning)'
        };

        Toastify({
            ...this.config,
            text: message,
            className: `toast-${type}`,
            style: {
                ...this.config.style,
                background: typeColors[type] || typeColors.success
            }
        }).showToast();
    }

    static success(message) { this.show(message, 'success'); }
    static error(message) { this.show(message, 'error'); }
    static warning(message) { this.show(message, 'warning'); }
}

/* ===== IMAGE PREVIEW MANAGER ===== */
class ImagePreviewManager {
    constructor() {
        this.selectedGalleryFiles = [];
        this.maxGalleryImages = 20;
        this.allowedTypes = CONFIG.ALLOWED_IMAGE_TYPES;
        this.maxFileSize = CONFIG.MAX_FILE_SIZE;
    }

    initialize() {
        this.setupMainImagePreview();
        this.setupGalleryPreview();
        this.setupCharacterCounter();
        console.log('Image Preview Manager initialized');
    }

    /* === MAIN IMAGE PREVIEW === */
    setupMainImagePreview() {
        const mainImageInput = document.getElementById('image');
        const previewContainer = document.getElementById('main-image-preview');
        const previewImg = previewContainer?.querySelector('img');
        const removeBtn = document.getElementById('remove-main-image');

        if (!mainImageInput) return;

        mainImageInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (!file) return this.hideMainImagePreview();

            if (!this.validateFile(file)) {
                mainImageInput.value = '';
                return this.hideMainImagePreview();
            }

            this.showMainImagePreview(file, previewImg, previewContainer);
        });

        removeBtn?.addEventListener('click', () => {
            mainImageInput.value = '';
            this.hideMainImagePreview();
            ToastManager.warning('Main image removed');
        });
    }

    showMainImagePreview(file, imgElement, container) {
        const reader = new FileReader();
        reader.onload = (e) => {
            if (imgElement && container) {
                imgElement.src = e.target.result;
                imgElement.alt = file.name;
                container.classList.remove('hidden');

                imgElement.classList.add('opacity-0');
                imgElement.onload = () => {
                    imgElement.classList.remove('opacity-0');
                    imgElement.classList.add('transition-opacity', 'duration-300');
                };
            }
        };
        reader.onerror = () => ToastManager.error('Failed to load image preview');
        reader.readAsDataURL(file);
    }

    hideMainImagePreview() {
        const previewContainer = document.getElementById('main-image-preview');
        const previewImg = previewContainer?.querySelector('img');
        previewContainer?.classList.add('hidden');
        if (previewImg) { previewImg.src = ''; previewImg.alt = ''; }
    }

    /* === GALLERY PREVIEW === */
    setupGalleryPreview() {
        const galleryInput = document.getElementById('gallery');
        if (!galleryInput) return;

        galleryInput.addEventListener('change', (e) => {
            const newFiles = Array.from(e.target.files);
            const validFiles = newFiles.filter(file => this.validateFile(file));

            if (validFiles.length !== newFiles.length) {
                galleryInput.value = '';
                return;
            }

            if (this.selectedGalleryFiles.length + validFiles.length > this.maxGalleryImages) {
                ToastManager.warning(`Maximum ${this.maxGalleryImages} images allowed`);
                galleryInput.value = '';
                return;
            }

            this.selectedGalleryFiles.push(...validFiles);
            this.updateGalleryPreview();
            this.updateGalleryInput();
        });
    }

    updateGalleryPreview() {
        const previewContainer = document.getElementById('gallery-preview');
        const countContainer = document.getElementById('gallery-count');
        const countText = document.getElementById('gallery-count-text');

        if (!previewContainer) return;
        previewContainer.innerHTML = '';

        this.selectedGalleryFiles.forEach((file, index) => {
            this.createGalleryPreviewItem(file, index, previewContainer);
        });

        if (countContainer && countText) {
            countContainer.classList.toggle('hidden', this.selectedGalleryFiles.length === 0);
            countText.textContent = `${this.selectedGalleryFiles.length} images selected`;
        }
    }

    createGalleryPreviewItem(file, index, container) {
        const reader = new FileReader();
        reader.onload = (e) => {
            const item = document.createElement('div');
            item.className = 'relative group bg-gray-700 rounded-lg overflow-hidden';
            item.innerHTML = `
                <img src="${e.target.result}" alt="Gallery ${index + 1}" 
                     class="w-full h-24 object-cover transition-transform duration-300 group-hover:scale-110">
                <button type="button" class="absolute top-1 right-1 bg-red-600 hover:bg-red-700 text-white rounded-full w-6 h-6 flex items-center justify-center opacity-0 group-hover:opacity-100" 
                        title="Remove" data-index="${index}">
                    <i class="fas fa-times text-xs"></i>
                </button>
            `;
            container.appendChild(item);

            item.querySelector('button').addEventListener('click', () => this.removeGalleryImage(index));
        };
        reader.readAsDataURL(file);
    }

    removeGalleryImage(index) {
        const removed = this.selectedGalleryFiles.splice(index, 1);
        this.updateGalleryPreview();
        this.updateGalleryInput();
        if (removed[0]) ToastManager.warning(`Removed: ${removed[0].name}`);
    }

    updateGalleryInput() {
        const galleryInput = document.getElementById('gallery');
        if (!galleryInput) return;
        const dt = new DataTransfer();
        this.selectedGalleryFiles.forEach(file => dt.items.add(file));
        galleryInput.files = dt.files;
    }

    /* === CHARACTER COUNTER === */
    setupCharacterCounter() {
        const input = document.getElementById('short_description');
        const counter = document.getElementById('shortDescCounter');
        if (!input || !counter) return;

        const maxLength = parseInt(input.getAttribute('maxlength')) || 150;
        const updateCounter = () => {
            const len = input.value.length;
            counter.textContent = `${len}/${maxLength}`;
            counter.className = len > maxLength * 0.9 ? 'text-red-400' :
                                len > maxLength * 0.7 ? 'text-yellow-400' : 'text-gray-400';
        };
        input.addEventListener('input', updateCounter);
        input.addEventListener('paste', () => setTimeout(updateCounter, 0));
        updateCounter();
    }

    validateFile(file) {
        if (!this.allowedTypes.includes(file.type)) {
            ToastManager.error(`Invalid file type: ${file.name}`);
            return false;
        }
        if (file.size > this.maxFileSize) {
            ToastManager.error(`File too large: ${file.name} (${(file.size/1024/1024).toFixed(1)}MB). Max 2MB`);
            return false;
        }
        return true;
    }

    resetPreviews() {
        this.hideMainImagePreview();
        this.selectedGalleryFiles = [];
        this.updateGalleryPreview();
        ['image', 'gallery'].forEach(id => { const el = document.getElementById(id); if (el) el.value = ''; });
    }
}

/* ===== SIDEBAR MANAGER ===== */
class SidebarManager {
    constructor() {
        this.sidebar = document.getElementById('sidebar');
        this.overlay = document.getElementById('sidebar-overlay');
        this.menuButton = document.getElementById('mobile-menu-button');
        this.closeButton = document.getElementById('close-sidebar');
        this.menuIcon = document.getElementById('menu-icon');
        this.isOpen = false;
    }

    initialize() {
        if (!this.sidebar || !this.overlay || !this.menuButton) {
            console.warn('Mobile sidebar elements not found');
            return;
        }

        this.setupEventListeners();
        this.setupKeyboardNavigation();
        this.setupClickOutside();
        console.log('Sidebar Manager initialized');
    }

    setupEventListeners() {
        // Menu button click
        this.menuButton.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            this.toggle();
        });

        // Close button click
        this.closeButton?.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            this.close();
        });

        // Overlay click
        this.overlay.addEventListener('click', () => {
            this.close();
        });

        // Navigation links click (auto close on mobile)
        this.sidebar.querySelectorAll('nav a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 1024) {
                    setTimeout(() => this.close(), 150);
                }
            });
        });

        // Window resize handler
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024 && this.isOpen) {
                this.close();
            }
        });
    }

    setupKeyboardNavigation() {
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isOpen) {
                this.close();
            }
        });
    }

    setupClickOutside() {
        document.addEventListener('click', (e) => {
            if (this.isOpen && 
                !this.sidebar.contains(e.target) && 
                !this.menuButton.contains(e.target)) {
                this.close();
            }
        });
    }

    toggle() {
        this.isOpen ? this.close() : this.open();
    }

    open() {
        this.isOpen = true;
        this.sidebar.classList.remove('translate-x-full');
        this.overlay.classList.remove('hidden');
        this.menuIcon.classList.remove('fa-bars');
        this.menuIcon.classList.add('fa-times');
        document.body.style.overflow = 'hidden';
        
        // Focus first menu item for accessibility
        const firstLink = this.sidebar.querySelector('nav a');
        firstLink?.focus();
    }

    close() {
        this.isOpen = false;
        this.sidebar.classList.add('translate-x-full');
        this.overlay.classList.add('hidden');
        this.menuIcon.classList.remove('fa-times');
        this.menuIcon.classList.add('fa-bars');
        document.body.style.overflow = '';
    }
}

/* ===== UI MANAGER ===== */
class UIManager {
    static initialize() {
        this.initDropdowns();
        this.initFileInputLabels();
        this.initMobileSidebar();
    }

    static initMobileSidebar() {
        window.sidebarManager = new SidebarManager();
        window.sidebarManager.initialize();
    }

    static initDropdowns() {
        document.querySelectorAll(CONFIG.SELECTORS.DROPDOWN_TOGGLE).forEach(dropdown => {
            dropdown.addEventListener('click', (e) => {
                e.preventDefault();
                dropdown.nextElementSibling?.classList.toggle('hidden');
            });
        });
    }

    static initFileInputLabels() {
        document.querySelectorAll(CONFIG.SELECTORS.FILE_INPUT).forEach(input => {
            input.addEventListener('change', () => {
                const label = input.nextElementSibling;
                if (label?.classList.contains('admin-file-label')) {
                    const span = label.querySelector('span');
                    if (span) span.textContent = input.files[0]?.name || 'Choose an image';
                }
            });
        });
    }
}


/* ===== RICH TEXT EDITOR MANAGER ===== */
class EditorManager {
    static initialize() {
        if (typeof ClassicEditor === 'undefined') return;
        document.querySelectorAll(CONFIG.SELECTORS.RICH_EDITOR).forEach(editor => {
            ClassicEditor.create(editor, {
                toolbar: ['heading','|','bold','italic','link','bulletedList','numberedList','blockQuote','insertTable','mediaEmbed','|','undo','redo'],
                heading: { options: [
                    { model: 'paragraph', title: 'Paragraph' },
                    { model: 'heading1', view: 'h1', title: 'Heading 1' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3' }
                ]}
            }).catch(err => {
                console.error(err);
                ToastManager.error('Failed to load editor');
            });
        });
    }
}

/* ===== FILE UPLOAD MANAGER ===== */
class FileUploadManager {
    static initialize() {
        this.initPreviewUploads();
        this.initLogoUploads();
    }

    static initPreviewUploads() {
        document.querySelectorAll(CONFIG.SELECTORS.ADMIN_FILE_INPUT).forEach(input => {
            input.addEventListener('change', function() {
                const preview = document.getElementById(this.dataset.preview);
                if (preview && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = e => preview.src = e.target.result;
                    reader.readAsDataURL(this.files[0]);
                }
            });
        });
    }

    static initLogoUploads() {
        document.querySelectorAll('input[type="file"][data-type]').forEach(input => {
            const newInput = input.cloneNode(true);
            input.parentNode.replaceChild(newInput, input);
            newInput.addEventListener('change', this.handleLogoUpload.bind(this));
        });
    }

    static async handleLogoUpload(e) {
        const input = e.target;
        const file = input.files[0];
        if (!file || !this.validateFile(file)) return;

        const type = input.dataset.type;
        const img = document.getElementById(type === 'logo' ? 'mainLogo' : 'footerLogo');

        this.setLoadingState(input, img, true);
        try {
            await this.uploadLogo(file, type, img);
        } catch (err) {
            ToastManager.error(err.message || 'Upload error');
        } finally {
            this.setLoadingState(input, img, false);
        }
    }

    static validateFile(file) {
        if (!CONFIG.ALLOWED_IMAGE_TYPES.includes(file.type)) {
            ToastManager.error('Invalid image type'); return false;
        }
        if (file.size > CONFIG.MAX_FILE_SIZE) {
            ToastManager.error('Max size 2MB'); return false;
        }
        return true;
    }

    static setLoadingState(input, img, state) {
        input.disabled = state;
        if (img) img.style.opacity = state ? '0.5' : '1';
        if (!state) input.value = '';
    }

    static async uploadLogo(file, type, img) {
        const token = document.querySelector('meta[name="csrf-token"]');
        if (!token) throw new Error('CSRF token missing');

        const fd = new FormData();
        fd.append('logo', file);
        fd.append('type', type);

        const res = await fetch(CONFIG.API_ENDPOINTS.UPLOAD_LOGO, {
            method: 'POST',
            body: fd,
            headers: {
                'X-CSRF-TOKEN': token.content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!res.ok) throw new Error(`Upload failed (${res.status})`);
        const data = await res.json();
        if (!data.success) throw new Error(data.message || 'Upload failed');

        this.updateLogoElements(data.logo_url, type, img);
        ToastManager.success('Logo updated');
    }

    static updateLogoElements(url, type, img) {
        const newUrl = `${url}?t=${Date.now()}`;
        if (img) img.src = newUrl;
        document.querySelectorAll(`.site-logo[data-type="${type}"]`).forEach(el => el.src = newUrl);
    }
}

/* ===== CONTACT MANAGER ===== */
class ContactManager {
    static async updateStatus(contactId, status) {
        try {
            const response = await fetch(`/admin/contacts/${contactId}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: status })
            });
            
            const data = await response.json();
            
            if (data.success) {
                ToastManager.success('Status updated successfully');
                // Refresh the page to show updated status
                setTimeout(() => location.reload(), 1000);
            } else {
                ToastManager.error(data.message || 'Failed to update status');
            }
        } catch (error) {
            console.error('Error updating contact status:', error);
            ToastManager.error('Failed to update status');
        }
    }

    static async markAsRead(contactId) {
        return this.updateStatus(contactId, 'read');
    }

    static async markAsReplied(contactId) {
        return this.updateStatus(contactId, 'replied');
    }

    static async markAllAsRead() {
        try {
            const response = await fetch('/admin/contacts/mark-all-read', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                ToastManager.success(`${data.count} messages marked as read`);
                // Refresh the page to show updated statuses
                setTimeout(() => location.reload(), 1000);
            } else {
                ToastManager.error(data.message || 'Failed to mark all as read');
            }
        } catch (error) {
            console.error('Error marking all as read:', error);
            ToastManager.error('Failed to mark all as read');
        }
    }
}


/* ===== MAIN ADMIN PANEL ===== */
class AdminPanel {
    static initialize() {
        try {
            UIManager.initialize();

            EditorManager.initialize();
            FileUploadManager.initialize();
            if (document.getElementById('image') || document.getElementById('gallery')) {
                window.imagePreviewManager = new ImagePreviewManager();
                window.imagePreviewManager.initialize();
            }
            console.log('Admin Panel ready');
        } catch (err) {
            console.error(err);
            ToastManager.error('Error loading admin panel');
        }
    }

}

/* ===== GLOBAL ERROR HANDLER ===== */
window.addEventListener('error', e => {
    console.error('Global error:', e.error);
    ToastManager.error('Unexpected error');
});
window.addEventListener('unhandledrejection', e => {
    console.error('Promise rejection:', e.reason);
    ToastManager.error('Operation failed');
});

/* ===== INITIALIZATION ===== */
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', AdminPanel.initialize);
} else {
    AdminPanel.initialize();
}

/* ===== MESSAGE UTILS ===== */
class MessageUtils {
    static toggleMessage(contactId) {
        const fullMessage = document.getElementById(`full-message-${contactId}`);
        const toggleText = document.getElementById(`toggle-text-${contactId}`);
        
        if (fullMessage && toggleText) {
            if (fullMessage.classList.contains('hidden')) {
                fullMessage.classList.remove('hidden');
                toggleText.textContent = 'Show less';
            } else {
                fullMessage.classList.add('hidden');
                toggleText.textContent = 'Show more';
            }
        }
    }
}

/* ===== EXPORT FOR EXTERNAL USE ===== */
window.AdminPanel = {
    toast: ToastManager,
    fileUpload: FileUploadManager,
    imagePreview: ImagePreviewManager,
    contact: ContactManager,
    message: MessageUtils
};

// Global function for backward compatibility
window.toggleMessage = MessageUtils.toggleMessage;
