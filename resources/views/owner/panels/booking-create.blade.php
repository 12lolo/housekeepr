<!-- Booking Create Panel -->
<div class="neu-modal-overlay" id="bookingCreateModal">
    <div class="neu-modal">
        <div class="neu-modal-header">
            <h3>Nieuwe Boeking</h3>
            <button class="neu-modal-close" type="button" onclick="closeModal('bookingCreateModal')">
                <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>

        <div class="neu-modal-body">
            <form action="{{ route('owner.bookings.store') }}" method="POST" id="bookingCreateForm" onsubmit="submitBookingForm(event)">
                @csrf

                <div class="neu-form-group">
                    <label for="booking_room_id" class="neu-label">Kamer *</label>
                    <select id="booking_room_id" name="room_id" class="neu-input" required>
                        <option value="">Selecteer kamer</option>
                        @foreach($hotel->rooms as $room)
                            <option value="{{ $room->id }}">
                                Kamer {{ $room->room_number }} - {{ ucfirst($room->type) }}
                            </option>
                        @endforeach
                    </select>
                    <span id="error_room_id" class="error-text" style="display: none;"></span>
                </div>

                <div class="neu-form-group">
                    <label for="guest_name" class="neu-label">Gastnaam *</label>
                    <input
                        type="text"
                        id="guest_name"
                        name="guest_name"
                        class="neu-input"
                        placeholder="Voor- en achternaam"
                        required
                    >
                    <span id="error_guest_name" class="error-text" style="display: none;"></span>
                </div>

                <div class="neu-form-group">
                    <label for="check_in" class="neu-label">Check-in *</label>
                    <input
                        type="date"
                        id="check_in"
                        name="check_in"
                        class="neu-input"
                        required
                    >
                    <span id="error_check_in" class="error-text" style="display: none;"></span>
                </div>

                <div class="neu-form-group">
                    <label for="check_out" class="neu-label">Check-out *</label>
                    <input
                        type="date"
                        id="check_out"
                        name="check_out"
                        class="neu-input"
                        required
                    >
                    <span id="error_check_out" class="error-text" style="display: none;"></span>
                </div>

                <div class="neu-modal-footer">
                    <button type="button" class="neu-button-secondary" onclick="closeModal('bookingCreateModal')" id="bookingCancelBtn">Annuleren</button>
                    <button type="submit" class="neu-button-primary" id="bookingSubmitBtn">
                        Boeking toevoegen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

