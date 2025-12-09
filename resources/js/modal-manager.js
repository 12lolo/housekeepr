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
window.editRoom = function(roomId, roomNumber, type, cleaningDuration, checkoutTime, checkinTime) {
    const form = document.getElementById('roomEditForm');
    form.action = `/owner/rooms/${roomId}`;

    document.getElementById('edit_room_number').value = roomNumber;
    document.getElementById('edit_room_type').value = type;
    document.getElementById('edit_cleaning_duration').value = cleaningDuration;
    document.getElementById('edit_checkout_time').value = checkoutTime;
    document.getElementById('edit_checkin_time').value = checkinTime;

    window.openModal('roomEditModal');
};

// Edit booking - populate form with data
window.editBooking = function(bookingId, roomId, guestName, checkIn, checkOut, notes) {
    const form = document.getElementById('bookingEditForm');
    form.action = `/owner/bookings/${bookingId}`;

    document.getElementById('edit_booking_room_id').value = roomId;
    document.getElementById('edit_guest_name').value = guestName;
    document.getElementById('edit_check_in').value = checkIn;
    document.getElementById('edit_check_out').value = checkOut;
    document.getElementById('edit_notes').value = notes || '';

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

