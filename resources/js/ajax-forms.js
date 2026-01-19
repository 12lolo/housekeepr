/**
 * AJAX Form Handler for HouseKeepr
 * Handles form submissions without page reload using Alpine.js
 */

// Alpine.js data for form handling
document.addEventListener('alpine:init', () => {
    Alpine.data('ajaxForm', (modalId, onSuccess) => ({
        loading: false,
        errors: {},
        message: '',
        messageType: '',

        async submitForm(event) {
            event.preventDefault();

            const form = event.target;
            const formData = new FormData(form);
            const url = form.action;
            const method = form.method || 'POST';

            // Reset state
            this.loading = true;
            this.errors = {};
            this.message = '';

            try {
                // Get CSRF token from meta tag or form
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
                    || formData.get('_token');

                const response = await fetch(url, {
                    method: method,
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    // Success
                    this.message = data.message || 'Succesvol opgeslagen!';
                    this.messageType = 'success';

                    // Close modal
                    if (modalId) {
                        closeModal(modalId);
                    }

                    // Reset form
                    form.reset();

                    // Show success toast
                    showToast(this.message, 'success');

                    // Reload page after short delay to show updates
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);

                } else if (response.status === 422) {
                    // Validation errors
                    this.errors = data.errors || {};
                    this.message = data.message || 'Controleer de invoervelden';
                    this.messageType = 'error';
                    showToast(this.message, 'error');

                } else {
                    // Other errors
                    this.message = data.message || 'Er is iets misgegaan';
                    this.messageType = 'error';
                    showToast(this.message, 'error');
                }

            } catch (error) {
                console.error('Form submission error:', error);
                this.message = 'Netwerkfout. Probeer het opnieuw.';
                this.messageType = 'error';
                showToast(this.message, 'error');

            } finally {
                this.loading = false;
            }
        },

        getError(field) {
            return this.errors[field] ? this.errors[field][0] : '';
        },

        hasError(field) {
            return !!this.errors[field];
        }
    }));
});

// Toast notification system
function showToast(message, type = 'success') {
    // Remove existing toasts
    const existing = document.querySelector('.toast-notification');
    if (existing) {
        existing.remove();
    }

    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast-notification toast-${type}`;
    toast.innerHTML = `
        <div class="toast-content">
            <span class="toast-icon">
                ${type === 'success' ? '✓' : '✕'}
            </span>
            <span class="toast-message">${message}</span>
        </div>
    `;

    // Add to body
    document.body.appendChild(toast);

    // Animate in
    setTimeout(() => toast.classList.add('show'), 10);

    // Remove after 3 seconds
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Delete confirmation with AJAX
async function confirmDelete(url, itemName, onSuccess) {
    if (!confirm(`Weet je zeker dat je "${itemName}" wilt verwijderen?`)) {
        return;
    }

    try {
        const response = await fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        });

        const data = await response.json();

        if (response.ok) {
            showToast(data.message || 'Succesvol verwijderd!', 'success');

            // Reload page after short delay
            setTimeout(() => {
                window.location.reload();
            }, 500);

        } else {
            showToast(data.message || 'Verwijderen mislukt', 'error');
        }

    } catch (error) {
        console.error('Delete error:', error);
        showToast('Netwerkfout. Probeer het opnieuw.', 'error');
    }
}
