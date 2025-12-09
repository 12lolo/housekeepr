<!-- Issue Create Panel -->
<div class="neu-modal-overlay" id="issueCreateModal">
    <div class="neu-modal">
        <div class="neu-modal-header">
            <h3>Nieuw Probleem</h3>
            <button class="neu-modal-close" type="button" onclick="closeModal('issueCreateModal')">
                <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>

        <div class="neu-modal-body">
            <form action="{{ route('owner.issues.store') }}" method="POST" enctype="multipart/form-data" id="issueCreateForm">
                @csrf

                <div class="neu-form-group">
                    <label for="issue_room_id" class="neu-label">Kamer *</label>
                    <select id="issue_room_id" name="room_id" class="neu-input" required>
                        <option value="">Selecteer kamer</option>
                        @foreach($hotel->rooms as $room)
                            <option value="{{ $room->id }}">Kamer {{ $room->room_number }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="neu-form-group">
                    <label for="issue_description" class="neu-label">Beschrijving *</label>
                    <textarea
                        id="issue_description"
                        name="description"
                        class="neu-input"
                        rows="4"
                        placeholder="Beschrijf het probleem..."
                        required
                    ></textarea>
                </div>

                <div class="neu-form-group">
                    <label for="issue_impact" class="neu-label">Impact *</label>
                    <select id="issue_impact" name="impact" class="neu-input" required>
                        <option value="geen_haast">Geen haast</option>
                        <option value="graag_snel">Graag snel</option>
                        <option value="kan_niet_gebruikt">Kamer kan niet gebruikt worden</option>
                    </select>
                </div>

                <div class="neu-form-group">
                    <label for="issue_photo" class="neu-label">Foto</label>
                    <input
                        type="file"
                        id="issue_photo"
                        name="photo"
                        class="neu-input"
                        accept="image/*"
                    >
                </div>

                <div class="neu-modal-footer">
                    <button type="button" class="neu-button-secondary" onclick="closeModal('issueCreateModal')">Annuleren</button>
                    <button type="submit" class="neu-button-primary">Probleem melden</button>
                </div>
            </form>
        </div>
    </div>
</div>

