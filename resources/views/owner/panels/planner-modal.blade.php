{{-- Manual Planner Modal --}}
<div class="neu-modal-overlay" id="plannerModal">
    <div class="neu-modal">
        <div class="neu-modal-header">
            <h3>Planning Handmatig Uitvoeren</h3>
            <button class="neu-modal-close" onclick="closeModal('plannerModal')" aria-label="Sluiten">
                <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>

        <form action="{{ route('owner.planner.run') }}" method="POST" id="plannerForm">
            @csrf

            <div class="neu-modal-body">
                <p class="form-description">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20" style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    Voer de planner handmatig uit voor een specifieke datum. Dit zal alle boekingen voor die datum opnieuw inplannen met de huidige capaciteit en schoonmakers.
                </p>

                <div class="form-group">
                    <label for="plan_date" class="form-label">
                        Datum
                        <span class="required">*</span>
                    </label>
                    <input
                        type="date"
                        id="plan_date"
                        name="date"
                        class="form-input"
                        required
                        min="{{ date('Y-m-d') }}"
                        value="{{ date('Y-m-d') }}"
                    />
                    <p class="form-help-text">Selecteer de datum waarvoor u de planning wilt uitvoeren</p>
                </div>

                <div class="neu-alert info">
                    <div class="alert-icon">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="alert-content">
                        <div class="alert-title">Let op</div>
                        <div class="alert-message">
                            Bestaande taken voor deze datum worden opnieuw ingepland. Taken die al in uitvoering of voltooid zijn worden niet aangepast.
                        </div>
                    </div>
                </div>
            </div>

            <div class="neu-modal-footer">
                <button type="button" class="neu-button-secondary" onclick="closeModal('plannerModal')">
                    Annuleren
                </button>
                <button type="submit" class="neu-button-primary">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
                    </svg>
                    Planning Uitvoeren
                </button>
            </div>
        </form>
    </div>
</div>
