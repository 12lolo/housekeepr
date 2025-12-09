<!-- Room Create Panel -->
<div class="neu-modal-overlay" id="roomCreateModal">
    <div class="neu-modal">
        <div class="neu-modal-header">
            <h3>Nieuwe Kamer</h3>
            <button class="neu-modal-close" type="button" onclick="closeModal('roomCreateModal')">
                <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>

        <div class="neu-modal-body">
            <form action="{{ route('owner.rooms.store') }}" method="POST" id="roomCreateForm">
                @csrf

                <!-- UC-R3: Uniek kamernummer -->
                <div class="neu-form-group">
                    <label for="room_number" class="neu-label">Kamernummer *</label>
                    <input
                        type="text"
                        id="room_number"
                        name="room_number"
                        class="neu-input"
                        placeholder="Bijv. 101"
                        required
                    >
                    <small style="display: block; margin-top: 0.5rem; color: var(--text-muted); font-size: 0.875rem;">
                        Moet uniek zijn binnen je hotel
                    </small>
                </div>

                <!-- UC-R4: Kamertype -->
                <div class="neu-form-group">
                    <label for="room_type" class="neu-label">Kamertype</label>
                    <select id="room_type" name="room_type" class="neu-input">
                        <option value="">Selecteer type</option>
                        <option value="single">Eenpersoons</option>
                        <option value="double">Tweepersoons</option>
                        <option value="suite">Suite</option>
                        <option value="family">Familie</option>
                    </select>
                    <small style="display: block; margin-top: 0.5rem; color: var(--text-muted); font-size: 0.875rem;">
                        Tijden en duur worden automatisch ingevuld, maar kunnen aangepast worden
                    </small>
                </div>

                <!-- UC-R5: Standaardduur in minuten -->
                <div class="neu-form-group">
                    <label for="standard_duration" class="neu-label">Standaardduur schoonmaak (minuten) *</label>
                    <input
                        type="number"
                        id="standard_duration"
                        name="standard_duration"
                        class="neu-input"
                        placeholder="Bijv. 30"
                        min="1"
                        value="30"
                        required
                    >
                    <small style="display: block; margin-top: 0.5rem; color: var(--text-muted); font-size: 0.875rem;">
                        Geschatte tijd om deze kamer schoon te maken
                    </small>
                </div>

                <!-- UC-R6: Vaste check-out tijd -->
                <div class="neu-form-group">
                    <label for="checkout_time" class="neu-label">Check-out tijd *</label>
                    <input
                        type="time"
                        id="checkout_time"
                        name="checkout_time"
                        class="neu-input"
                        value="11:00"
                        required
                    >
                    <small style="display: block; margin-top: 0.5rem; color: var(--text-muted); font-size: 0.875rem;">
                        Tijd waarop gasten moeten uitchecken
                    </small>
                </div>

                <!-- UC-R7: Vaste check-in tijd -->
                <div class="neu-form-group">
                    <label for="checkin_time" class="neu-label">Check-in tijd *</label>
                    <input
                        type="time"
                        id="checkin_time"
                        name="checkin_time"
                        class="neu-input"
                        value="15:00"
                        required
                    >
                    <small style="display: block; margin-top: 0.5rem; color: var(--text-muted); font-size: 0.875rem;">
                        Tijd waarop nieuwe gasten kunnen inchecken
                    </small>
                </div>

                <div class="neu-modal-footer">
                    <button type="button" class="neu-button-secondary" onclick="closeModal('roomCreateModal')">Annuleren</button>
                    <button type="submit" class="neu-button-primary">Kamer toevoegen</button>
                </div>
            </form>
        </div>

        <script>
        (function() {
            const form = document.getElementById('roomCreateForm');
            if (!form) return;

            const roomTypeSelect = form.querySelector('#room_type');
            const durationInput = form.querySelector('#standard_duration');
            const checkoutInput = form.querySelector('#checkout_time');
            const checkinInput = form.querySelector('#checkin_time');

            // Default times per room type
            const roomTypeDefaults = {
                'single': {
                    duration: 20,
                    checkout: '11:00',
                    checkin: '14:00'
                },
                'double': {
                    duration: 30,
                    checkout: '11:00',
                    checkin: '15:00'
                },
                'suite': {
                    duration: 45,
                    checkout: '11:00',
                    checkin: '16:00'
                },
                'family': {
                    duration: 40,
                    checkout: '10:30',
                    checkin: '15:30'
                }
            };

            // Update times when room type changes
            roomTypeSelect?.addEventListener('change', function() {
                const type = this.value;
                if (type && roomTypeDefaults[type]) {
                    const defaults = roomTypeDefaults[type];
                    durationInput.value = defaults.duration;
                    checkoutInput.value = defaults.checkout;
                    checkinInput.value = defaults.checkin;
                }
            });

            // Handle form submission with AJAX
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(form);
                const submitButton = form.querySelector('button[type="submit"]');

                // Disable submit button
                submitButton.disabled = true;
                submitButton.textContent = 'Bezig...';

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw data;
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    // Success - close modal and show toast
                    closeModal('roomCreateModal');
                    showToast(data.message || 'Kamer succesvol aangemaakt.', 'success');

                    // Reset form
                    form.reset();

                    // Reload page to show new room
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                })
                .catch(error => {
                    // Error - keep modal open and show errors
                    if (error.errors) {
                        // Validation errors
                        let errorMessage = 'Validatie fouten:\n';
                        Object.values(error.errors).forEach(msgs => {
                            errorMessage += '- ' + msgs.join(', ') + '\n';
                        });
                        showToast(errorMessage, 'error', 5000);
                    } else if (error.message) {
                        showToast(error.message, 'error', 5000);
                    } else {
                        showToast('Er is een fout opgetreden. Probeer opnieuw.', 'error', 5000);
                    }
                })
                .finally(() => {
                    // Re-enable submit button
                    submitButton.disabled = false;
                    submitButton.textContent = 'Kamer toevoegen';
                });
            });
        })();
        </script>
    </div>
</div>

