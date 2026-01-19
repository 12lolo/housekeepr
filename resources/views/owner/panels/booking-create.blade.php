<!-- Booking Create Panel -->
<div class="neu-modal-overlay" id="bookingCreateModal" x-data="ajaxForm('bookingCreateModal')">
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
            <form action="{{ route('owner.bookings.store') }}" method="POST" id="bookingCreateForm"
                  @submit="submitForm($event)" :class="{ 'form-loading': loading }">
                @csrf

                <div class="neu-form-group">
                    <label for="booking_room_id" class="neu-label">Kamer *</label>
                    <select id="booking_room_id" name="room_id" class="neu-input" required
                            :class="{ 'has-error': hasError('room_id') }">
                        <option value="">Selecteer kamer</option>
                        @foreach($hotel->rooms as $room)
                            <option value="{{ $room->id }}">
                                Kamer {{ $room->room_number }} - {{ ucfirst($room->type) }}
                            </option>
                        @endforeach
                    </select>
                    <span x-show="hasError('room_id')" x-text="getError('room_id')" class="error-text"></span>
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
                        :class="{ 'has-error': hasError('guest_name') }"
                    >
                    <span x-show="hasError('guest_name')" x-text="getError('guest_name')" class="error-text"></span>
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
                </div>

                <div class="neu-form-group">
                    <label for="notes" class="neu-label">Opmerkingen</label>
                    <textarea
                        id="notes"
                        name="notes"
                        class="neu-input"
                        rows="3"
                        placeholder="Bijv. late check-in, extra handdoeken..."
                    ></textarea>
                </div>

                <div class="neu-modal-footer">
                    <button type="button" class="neu-button-secondary" onclick="closeModal('bookingCreateModal')" :disabled="loading">Annuleren</button>
                    <button type="submit" class="neu-button-primary" :disabled="loading">
                        <span x-show="!loading">Boeking toevoegen</span>
                        <span x-show="loading">Bezig met opslaan...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

