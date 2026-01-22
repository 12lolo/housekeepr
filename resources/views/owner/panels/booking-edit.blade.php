<!-- Booking Edit Panel -->
<div class="neu-modal-overlay" id="bookingEditModal">
    <div class="neu-modal">
        <div class="neu-modal-header">
            <h3>Boeking bewerken</h3>
            <button class="neu-modal-close" type="button" onclick="closeModal('bookingEditModal')">
                <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>

        <div class="neu-modal-body">
            <form method="POST" id="bookingEditForm">
                @csrf
                @method('PUT')

                <div class="neu-form-group">
                    <label for="edit_booking_room_id" class="neu-label">Kamer *</label>
                    <select id="edit_booking_room_id" name="room_id" class="neu-input" required>
                        @foreach($hotel->rooms as $room)
                            <option value="{{ $room->id }}">
                                Kamer {{ $room->room_number }} - {{ ucfirst($room->type) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="neu-form-group">
                    <label for="edit_guest_name" class="neu-label">Gastnaam *</label>
                    <input
                        type="text"
                        id="edit_guest_name"
                        name="guest_name"
                        class="neu-input"
                        required
                    >
                </div>

                <div class="neu-form-group">
                    <label for="edit_check_in" class="neu-label">Check-in *</label>
                    <input
                        type="date"
                        id="edit_check_in"
                        name="check_in"
                        class="neu-input"
                        required
                    >
                </div>

                <div class="neu-form-group">
                    <label for="edit_check_out" class="neu-label">Check-out *</label>
                    <input
                        type="date"
                        id="edit_check_out"
                        name="check_out"
                        class="neu-input"
                        required
                    >
                </div>

                <div class="neu-modal-footer">
                    <button type="button" class="neu-button-secondary" onclick="closeModal('bookingEditModal')">Annuleren</button>
                    <button type="submit" class="neu-button-primary">Opslaan</button>
                </div>
            </form>
        </div>
    </div>
</div>

