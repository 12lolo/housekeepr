<!-- Cleaner Create Panel -->
<div class="neu-modal-overlay" id="cleanerCreateModal">
    <div class="neu-modal">
        <div class="neu-modal-header">
            <h3>Nieuwe Schoonmaker</h3>
            <button class="neu-modal-close" type="button" onclick="closeModal('cleanerCreateModal')">
                <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>

        <div class="neu-modal-body">
            <form action="{{ route('owner.cleaners.store') }}" method="POST" id="cleanerCreateForm">
                @csrf

                <div class="neu-form-group">
                    <label for="cleaner_name" class="neu-label">Naam *</label>
                    <input
                        type="text"
                        id="cleaner_name"
                        name="name"
                        class="neu-input"
                        placeholder="Voor- en achternaam"
                        required
                    >
                </div>

                <div class="neu-form-group">
                    <label for="cleaner_email" class="neu-label">Email *</label>
                    <input
                        type="email"
                        id="cleaner_email"
                        name="email"
                        class="neu-input"
                        placeholder="email@voorbeeld.nl"
                        required
                    >
                </div>

                <div class="neu-form-group">
                    <label for="cleaner_phone" class="neu-label">Telefoonnummer</label>
                    <input
                        type="tel"
                        id="cleaner_phone"
                        name="phone"
                        class="neu-input"
                        placeholder="06-12345678"
                    >
                </div>

                <div class="neu-modal-footer">
                    <button type="button" class="neu-button-secondary" onclick="closeModal('cleanerCreateModal')">Annuleren</button>
                    <button type="submit" class="neu-button-primary">Schoonmaker toevoegen</button>
                </div>
            </form>
        </div>
    </div>
</div>

