<!-- Cleaner Edit Panel -->
<div class="neu-modal-overlay" id="cleanerEditModal">
    <div class="neu-modal">
        <div class="neu-modal-header">
            <h3>Schoonmaker Bewerken</h3>
            <button class="neu-modal-close" type="button" onclick="closeModal('cleanerEditModal')">
                <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>

        <div class="neu-modal-body">
            <form method="POST" id="cleanerEditForm">
                @csrf
                @method('PUT')

                <div class="neu-form-group">
                    <label for="edit_cleaner_name" class="neu-label">Naam *</label>
                    <input
                        type="text"
                        id="edit_cleaner_name"
                        name="name"
                        class="neu-input"
                        placeholder="Voor- en achternaam"
                        required
                    >
                </div>

                <div class="neu-form-group">
                    <label for="edit_cleaner_email" class="neu-label">Email *</label>
                    <input
                        type="email"
                        id="edit_cleaner_email"
                        name="email"
                        class="neu-input"
                        placeholder="email@voorbeeld.nl"
                        required
                    >
                </div>

                <div class="neu-form-group">
                    <label for="edit_cleaner_phone" class="neu-label">Telefoonnummer</label>
                    <input
                        type="tel"
                        id="edit_cleaner_phone"
                        name="phone"
                        class="neu-input"
                        placeholder="06-12345678 of +31612345678"
                        pattern="[\+]?[(]?[0-9]{1,4}[)]?[-\s\.]?[(]?[0-9]{1,4}[)]?[-\s\.]?[0-9]{1,9}"
                        inputmode="tel"
                        title="Voer een geldig telefoonnummer in"
                    >
                    <small style="color: var(--text-muted, #6b7280); font-size: 0.75rem; margin-top: 0.25rem; display: block;">
                        Formaten: 06-12345678, +31612345678, (06)12345678
                    </small>
                </div>

                <div class="neu-form-group">
                    <label class="neu-label">Beschikbare dagen *</label>
                    <small style="color: var(--text-muted, #6b7280); font-size: 0.75rem; margin-bottom: 0.75rem; display: block;">
                        Selecteer de dagen waarop deze schoonmaker beschikbaar is
                    </small>
                    <div id="edit-availability-container" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 0.75rem;">
                        @foreach(['Maandag' => 1, 'Dinsdag' => 2, 'Woensdag' => 3, 'Donderdag' => 4, 'Vrijdag' => 5, 'Zaterdag' => 6, 'Zondag' => 0] as $dayName => $dayNum)
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; padding: 0.75rem; border-radius: 8px; background: var(--neu-bg, #e0e5ec); transition: all 0.2s;">
                                <input
                                    type="checkbox"
                                    name="availability[]"
                                    value="{{ $dayNum }}"
                                    class="availability-checkbox"
                                    data-day="{{ $dayNum }}"
                                    style="width: 18px; height: 18px; cursor: pointer;"
                                >
                                <span style="font-size: 0.875rem; font-weight: 500;">{{ $dayName }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="neu-modal-footer">
                    <button type="button" class="neu-button-secondary" onclick="closeModal('cleanerEditModal')">Annuleren</button>
                    <button type="submit" class="neu-button-primary">Opslaan</button>
                </div>
            </form>
        </div>
    </div>
</div>

