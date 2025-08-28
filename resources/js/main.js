/**
 * Main JavaScript file for handling front-end functionality
 */

/* ===========================
    FORM UTILS
=========================== */

// Helper function to update button to success state
const updateButtonToSuccess = (submitBtn, message = 'Submitted Successfully!') => {
    submitBtn.className = submitBtn.className
        .replace(/bg-orange-\d+/g, 'bg-green-500')
        .replace(/hover:bg-orange-\d+/g, '')
        .replace('hover:bg-orange-600', '')
        .replace('transition-colors', '');
    submitBtn.innerHTML = `<i class="fas fa-check mr-2"></i>${message}`;
    submitBtn.style.cursor = 'default';
    submitBtn.style.backgroundColor = '#10b981'; // Force green color
    submitBtn.disabled = true;
};

// Helper function to generate form key
const generateFormKey = (form, email) => {
    const formData = new FormData(form);
    return `${form.action.split('/').pop()}_${email}${formData.get('job_id') ? '_' + formData.get('job_id') : ''}`;
};

/* ===========================
    FORM HANDLER
=========================== */

const initFormHandler = () => {
    const forms = document.querySelectorAll('form');
    const submittedForms = new Set(); // Track currently submitting forms
    
    forms.forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(form);
            const email = formData.get('email')?.toLowerCase().trim();
            const submitBtn = form.querySelector('button[type="submit"]');
            
            if (!email || !submitBtn) return;
            
            const formKey = generateFormKey(form, email);
            
            // Check if already submitted OR currently submitting
            if (localStorage.getItem(formKey) || submittedForms.has(formKey)) {
                console.log('Form already submitted or in progress');
                return;
            }
            
            // Mark as submitting
            submittedForms.add(formKey);
            
            const originalText = submitBtn.textContent;
            const originalClasses = submitBtn.className;
            
            // Show loading and disable
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sending...';
            
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Save to prevent future re-submission
                    localStorage.setItem(formKey, Date.now());
                    
                    // Update button to success state - PERMANENT
                    updateButtonToSuccess(submitBtn);
                    
                    // Reset form but keep button disabled
                    form.reset();
                } else {
                    // Remove from submitting set on error
                    submittedForms.delete(formKey);
                    // Reset button on error
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                    submitBtn.className = originalClasses;
                }
            } catch (error) {
                submittedForms.delete(formKey);
                // Reset button on error
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
                submitBtn.className = originalClasses;
                console.error('Form submission error:', error);
            }
        });
    });
    
    // Check for already submitted forms on page load
    setTimeout(() => {
        forms.forEach(form => {
            const emailInput = form.querySelector('input[name="email"]');
            const submitBtn = form.querySelector('button[type="submit"]');
            
            if (emailInput && submitBtn) {
                emailInput.addEventListener('blur', () => {
                    const email = emailInput.value?.toLowerCase().trim();
                    if (!email) return;
                    
                    const formKey = generateFormKey(form, email);
                    
                    if (localStorage.getItem(formKey)) {
                        updateButtonToSuccess(submitBtn, 'Already Submitted!');
                    }
                });
            }
        });
    }, 100);
};

/* ===========================
   INITIALIZATION
=========================== */

document.addEventListener('DOMContentLoaded', () => {
    initFormHandler();
});
