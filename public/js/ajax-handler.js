// Standalone AJAX Handler - For use without build process
document.addEventListener('DOMContentLoaded', function() {
    console.log('AJAX Handler loaded');
    setupAjaxHandlers();
});

function setupAjaxHandlers() {
    setupAjaxForms();
    setupAjaxButtons();
    setupDeleteButtons();
    setupModalForms();
}

// Show success/error messages
function showMessage(message, type = 'success') {
    // Remove any existing messages
    const existingMessage = document.querySelector('.ajax-message');
    if (existingMessage) {
        existingMessage.remove();
    }
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `ajax-message fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
    alertDiv.textContent = message;
    document.body.appendChild(alertDiv);
    
    // Fade in animation
    alertDiv.style.opacity = '0';
    alertDiv.style.transform = 'translateX(100%)';
    setTimeout(() => {
        alertDiv.style.transition = 'all 0.3s ease';
        alertDiv.style.opacity = '1';
        alertDiv.style.transform = 'translateX(0)';
    }, 10);
    
    // Auto remove
    setTimeout(() => {
        alertDiv.style.opacity = '0';
        alertDiv.style.transform = 'translateX(100%)';
        setTimeout(() => {
            alertDiv.remove();
        }, 300);
    }, 5000);
}

// Setup AJAX for regular forms
function setupAjaxForms() {
    const forms = document.querySelectorAll('form[data-ajax="true"]');
    forms.forEach(form => {
        form.addEventListener('submit', handleFormSubmit);
    });
}

// Setup AJAX for buttons with data-ajax
function setupAjaxButtons() {
    const buttons = document.querySelectorAll('button[data-ajax="true"], a[data-ajax="true"]');
    buttons.forEach(button => {
        button.addEventListener('click', handleButtonClick);
    });
}

// Setup delete buttons
function setupDeleteButtons() {
    const deleteButtons = document.querySelectorAll('button[data-delete="true"], form[data-delete="true"] button[type="submit"]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', handleDeleteClick);
    });
}

// Setup modal forms
function setupModalForms() {
    const modalForms = document.querySelectorAll('form[data-modal="true"]');
    modalForms.forEach(form => {
        form.addEventListener('submit', handleModalFormSubmit);
    });
}

// Handle form submission
async function handleFormSubmit(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');
    
    // Disable submit button
    if (submitButton) {
        submitButton.disabled = true;
        const originalText = submitButton.textContent;
        submitButton.textContent = 'Processing...';
        
        try {
            const response = await fetch(form.action, {
                method: form.method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            });
            
            const data = await response.json();
            
            if (response.ok && data.success) {
                showMessage(data.message);
                
                // Handle form reset if needed
                if (data.reset_form) {
                    form.reset();
                }
                
                // Redirect if specified
                if (data.redirect) {
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1000);
                } else if (data.reload) {
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
            } else {
                // Handle validation errors
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        const input = form.querySelector(`[name="${field}"]`);
                        if (input) {
                            showFieldError(input, data.errors[field][0]);
                        }
                    });
                    showMessage('Please correct the errors below', 'error');
                } else {
                    showMessage(data.message || 'An error occurred', 'error');
                }
            }
        } catch (error) {
            console.error('Form submission error:', error);
            showMessage('Network error occurred', 'error');
        } finally {
            submitButton.disabled = false;
            submitButton.textContent = originalText;
        }
    }
}

// Handle button clicks
async function handleButtonClick(e) {
    e.preventDefault();
    const element = e.target;
    const url = element.href || element.dataset.url;
    const method = element.dataset.method || 'GET';
    const confirm_message = element.dataset.confirm;
    
    if (confirm_message && !confirm(confirm_message)) {
        return;
    }
    
    // Disable button to prevent multiple clicks
    element.disabled = true;
    
    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                'Content-Type': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (response.ok && data.success) {
            showMessage(data.message);
            
            if (data.redirect) {
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1000);
            } else if (data.reload) {
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        } else {
            showMessage(data.message || 'An error occurred', 'error');
        }
    } catch (error) {
        console.error('Button click error:', error);
        showMessage('Network error occurred', 'error');
    } finally {
        element.disabled = false;
    }
}

// Handle delete clicks
async function handleDeleteClick(e) {
    e.preventDefault();
    const button = e.target;
    const form = button.closest('form');
    const confirm_message = button.dataset.confirm || 'Are you sure you want to delete this?';
    
    if (!confirm(confirm_message)) {
        return;
    }
    
    const formData = new FormData(form);
    
    // Disable button to prevent multiple submissions
    button.disabled = true;
    const originalText = button.textContent;
    button.textContent = 'Deleting...';
    
    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (response.ok && data.success) {
            showMessage(data.message);
            
            // Remove the element or reload
            if (data.remove_element) {
                const elementToRemove = document.querySelector(data.remove_element);
                if (elementToRemove) {
                    elementToRemove.remove();
                }
            } else if (data.reload) {
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else if (data.redirect) {
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1000);
            }
        } else {
            showMessage(data.message || 'An error occurred', 'error');
        }
    } catch (error) {
        console.error('Delete operation error:', error);
        showMessage('Network error occurred', 'error');
    } finally {
        button.disabled = false;
        button.textContent = originalText;
    }
}

// Handle modal form submission
async function handleModalFormSubmit(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');
    
    if (submitButton) {
        submitButton.disabled = true;
        const originalText = submitButton.textContent;
        submitButton.textContent = 'Processing...';
        
        try {
            const response = await fetch(form.action, {
                method: form.method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            });
            
            const data = await response.json();
            
            if (response.ok && data.success) {
                showMessage(data.message);
                
                // Close modal
                const modal = form.closest('.fixed');
                if (modal) {
                    modal.classList.add('hidden');
                }
                
                // Clear form
                form.reset();
                
                // Reload or redirect
                if (data.reload) {
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else if (data.redirect) {
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1000);
                }
            } else {
                // Handle validation errors
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        const input = form.querySelector(`[name="${field}"]`);
                        if (input) {
                            showFieldError(input, data.errors[field][0]);
                        }
                    });
                    showMessage('Please correct the errors below', 'error');
                } else {
                    showMessage(data.message || 'An error occurred', 'error');
                }
            }
        } catch (error) {
            console.error('Modal form submission error:', error);
            showMessage('Network error occurred', 'error');
        } finally {
            submitButton.disabled = false;
            submitButton.textContent = originalText;
        }
    }
}

// Show field error
function showFieldError(input, message) {
    // Remove existing error
    const existingError = input.parentNode.querySelector('.error-message');
    if (existingError) {
        existingError.remove();
    }
    
    // Add error class to input
    input.classList.add('border-red-500');
    
    // Create error message
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message text-red-500 text-sm mt-1';
    errorDiv.textContent = message;
    
    // Insert after input
    input.parentNode.insertBefore(errorDiv, input.nextSibling);
    
    // Remove error after 10 seconds
    setTimeout(() => {
        input.classList.remove('border-red-500');
        if (errorDiv.parentNode) {
            errorDiv.remove();
        }
    }, 10000);
}

// Export functions for global use
window.showMessage = showMessage;
window.setupAjaxHandlers = setupAjaxHandlers;
