/**
 * Modal Panel Management
 * Handles opening/closing of modal overlays in the dashboard
 */

// Open modal by ID
window.openModal = function(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
    }
};

// Close modal by ID
window.closeModal = function(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = ''; // Restore scrolling
    }
};

// Close modal when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    const modals = document.querySelectorAll('.neu-modal-overlay');

    modals.forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal(modal.id);
            }
        });
    });

    // Close modals on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            modals.forEach(modal => {
                if (modal.classList.contains('active')) {
                    closeModal(modal.id);
                }
            });
        }
    });
});

// Edit room - populate form with data
window.editRoom = function(roomId, roomNumber, type, standardDuration, checkoutTime, checkinTime) {
    const form = document.getElementById('roomEditForm');
    if (!form) {
        console.error('Room edit form not found');
        return;
    }

    form.action = `/owner/rooms/${roomId}`;

    const roomNumberInput = document.getElementById('edit_room_number');
    const roomTypeSelect = document.getElementById('edit_room_type');
    const durationInput = document.getElementById('edit_standard_duration');
    const checkoutInput = document.getElementById('edit_checkout_time');
    const checkinInput = document.getElementById('edit_checkin_time');

    if (roomNumberInput) roomNumberInput.value = roomNumber || '';
    if (roomTypeSelect) roomTypeSelect.value = type || '';
    if (durationInput) durationInput.value = standardDuration || 30;
    if (checkoutInput) checkoutInput.value = checkoutTime || '11:00';
    if (checkinInput) checkinInput.value = checkinTime || '15:00';

    window.openModal('roomEditModal');
};

// Edit booking - populate form with data
window.editBooking = function(bookingId, roomId, guestName, checkIn, checkOut) {
    const form = document.getElementById('bookingEditForm');
    form.action = `/owner/bookings/${bookingId}`;

    document.getElementById('edit_booking_room_id').value = roomId;
    document.getElementById('edit_guest_name').value = guestName;
    document.getElementById('edit_check_in').value = checkIn;
    document.getElementById('edit_check_out').value = checkOut;

    window.openModal('bookingEditModal');
};

// Delete confirmation
window.confirmDelete = function(formId, itemName) {
    if (confirm(`Weet je zeker dat je "${itemName}" wilt verwijderen? Dit kan niet ongedaan worden gemaakt.`)) {
        document.getElementById(formId).submit();
    }
};

// Form submission handlers with loading state
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form[id$="Form"]');

    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span>Bezig...</span>';
            }
        });
    });
});

