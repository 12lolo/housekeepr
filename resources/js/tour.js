/**
 * HouseKeepr Onboarding Tour
 * Guides new hotel owners through setting up their hotel
 */

class HousekeeprTour {
    constructor() {
        this.currentStep = 0;
        this.steps = [
            {
                target: '#addRoomBtn',
                title: 'Voeg Je Eerste Kamer Toe',
                description: 'Begin met het toevoegen van je eerste hotelkamer. Klik op deze knop om een kamer aan te maken.',
                section: 'kamers',
                badge: '1'
            },
            {
                target: '#addBookingBtn',
                title: 'Maak Een Boeking Aan',
                description: 'Na het toevoegen van kamers, kun je boekingen maken. Dit zorgt automatisch voor schoonmaaktaken.',
                section: 'boekingen',
                badge: '2'
            },
            {
                target: '#addCleanerBtn',
                title: 'Voeg Schoonmakers Toe',
                description: 'Voeg je schoonmakers toe zodat ze taken kunnen zien en uitvoeren via hun mobiele app.',
                section: 'schoonmakers',
                badge: '3'
            },
            {
                target: '.nav-section-link[data-section="schoonmaakplanning"]',
                title: 'Bekijk De Schoonmaakplanning',
                description: 'Hier zie je alle schoonmaaktaken die automatisch zijn aangemaakt op basis van je boekingen.',
                section: 'schoonmaakplanning',
                badge: '4'
            },
            {
                target: '#openProfileBtn',
                title: 'Je Profiel',
                description: 'Klik hier om je profiel te bekijken en je wachtwoord te wijzigen. Je kunt hier ook uitloggen.',
                section: null,
                badge: '5'
            }
        ];

        this.overlay = null;
        this.spotlight = null;
        this.tooltip = null;
        this.welcomeModal = null;
    }

    init() {
        // Check if tour should be shown (new hotel with no rooms)
        const shouldShowTour = this.shouldShowTour();

        if (shouldShowTour) {
            this.createElements();
            this.showWelcomeModal();
            this.addBadgesToButtons();
        }
    }

    shouldShowTour() {
        // Check if the required tour elements exist on the page
        // This ensures we're on the owner dashboard page
        const tourTargetsExist = document.querySelector('#addRoomBtn') !== null;

        if (!tourTargetsExist) {
            return false;
        }

        // Check if this is a new hotel (can be set from backend)
        const isNewHotel = document.body.dataset.newHotel === 'true';
        const hasRooms = parseInt(document.body.dataset.totalRooms || '0') > 0;

        // Get hotel ID for hotel-specific tour completion
        const hotelId = document.body.dataset.hotelId;

        if (hotelId) {
            const tourCompletedForHotel = localStorage.getItem(`housekeepr_tour_completed_hotel_${hotelId}`);
            if (tourCompletedForHotel) {
                return false; // Tour already completed for this hotel
            }
        }

        return isNewHotel || !hasRooms;
    }

    createElements() {
        // Create overlay
        this.overlay = document.createElement('div');
        this.overlay.className = 'tour-overlay';
        document.body.appendChild(this.overlay);

        // Create spotlight
        this.spotlight = document.createElement('div');
        this.spotlight.className = 'tour-spotlight';
        document.body.appendChild(this.spotlight);

        // Create tooltip
        this.tooltip = document.createElement('div');
        this.tooltip.className = 'tour-tooltip';
        document.body.appendChild(this.tooltip);
    }

    showWelcomeModal() {
        const modal = document.createElement('div');
        modal.className = 'tour-welcome-modal active';
        modal.innerHTML = `
            <div class="tour-welcome-icon">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/>
                </svg>
            </div>
            <h2>Welkom bij HouseKeepr! 🎉</h2>
            <p>
                Je hotel is succesvol aangemaakt! Laten we je helpen om alles in te stellen.
                Deze korte tour laat je zien hoe je kamers toevoegt, boekingen maakt en schoonmakers koppelt.
            </p>
            <div class="tour-welcome-actions">
                <button class="neu-button-secondary" id="tourSkipBtn">
                    Later
                </button>
                <button class="neu-button-primary" id="tourStartBtn">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" style="margin-right: 0.5rem;">
                        <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                    Start Tour
                </button>
            </div>
        `;

        this.welcomeModal = modal;
        this.overlay.classList.add('active');
        document.body.appendChild(modal);

        // Add event listeners after modal is added to DOM
        document.getElementById('tourSkipBtn').addEventListener('click', () => this.skipTour());
        document.getElementById('tourStartBtn').addEventListener('click', () => this.startTour());
    }

    startTour() {
        if (this.welcomeModal) {
            this.welcomeModal.remove();
        }
        this.currentStep = 0;
        this.showStep(this.currentStep);
    }

    skipTour() {
        this.endTour();
        // Mark tour as completed so it doesn't show again for this hotel
        const hotelId = document.body.dataset.hotelId;
        if (hotelId) {
            localStorage.setItem(`housekeepr_tour_completed_hotel_${hotelId}`, 'true');
        }
        // Also set global flag for backwards compatibility
        localStorage.setItem('housekeepr_tour_completed', 'true');
    }

    showStep(stepIndex) {
        const step = this.steps[stepIndex];

        if (!step) {
            this.endTour();
            return;
        }

        // Open the correct accordion section
        this.openSection(step.section);

        // Wait for section to open
        setTimeout(() => {
            const targetElement = document.querySelector(step.target);

            if (!targetElement) {
                console.warn('Tour target not found:', step.target);
                this.nextStep();
                return;
            }

            // Position spotlight
            this.positionSpotlight(targetElement);

            // Show tooltip
            this.showTooltip(step, targetElement);

            // Show overlay
            this.overlay.classList.add('active');
        }, 300);
    }

    openSection(sectionName) {
        // Skip if no section (e.g., for profile button in sidebar)
        if (!sectionName) {
            return;
        }

        const navLink = document.querySelector(`.nav-section-link[data-section="${sectionName}"]`);
        const header = document.querySelector(`.neu-accordion-header[data-section="${sectionName}"]`);

        if (navLink) {
            navLink.click();
        } else if (header && !header.classList.contains('active')) {
            header.click();
        }
    }

    positionSpotlight(element) {
        const rect = element.getBoundingClientRect();
        const padding = 12;

        this.spotlight.style.top = `${rect.top - padding}px`;
        this.spotlight.style.left = `${rect.left - padding}px`;
        this.spotlight.style.width = `${rect.width + padding * 2}px`;
        this.spotlight.style.height = `${rect.height + padding * 2}px`;
        this.spotlight.style.display = 'block';
    }

    showTooltip(step, targetElement) {
        const rect = targetElement.getBoundingClientRect();
        const totalSteps = this.steps.length;

        this.tooltip.innerHTML = `
            <div class="tour-tooltip-header">
                <div class="tour-step-badge">${step.badge}</div>
                <h3>${step.title}</h3>
            </div>
            <div class="tour-tooltip-body">
                ${step.description}
            </div>
            <div class="tour-tooltip-footer">
                <div class="tour-progress">Stap ${this.currentStep + 1} van ${totalSteps}</div>
                <div class="tour-actions">
                    ${this.currentStep > 0 ? '<button class="neu-button-secondary tour-prev-btn">Vorige</button>' : ''}
                    ${this.currentStep < totalSteps - 1
                        ? '<button class="neu-button-primary tour-next-btn">Volgende</button>'
                        : '<button class="neu-button-primary tour-end-btn">Afronden</button>'
                    }
                </div>
            </div>
        `;

        // Add event listeners
        const prevBtn = this.tooltip.querySelector('.tour-prev-btn');
        const nextBtn = this.tooltip.querySelector('.tour-next-btn');
        const endBtn = this.tooltip.querySelector('.tour-end-btn');

        if (prevBtn) prevBtn.addEventListener('click', () => this.prevStep());
        if (nextBtn) nextBtn.addEventListener('click', () => this.nextStep());
        if (endBtn) endBtn.addEventListener('click', () => this.endTour());

        // Position tooltip
        const tooltipRect = this.tooltip.getBoundingClientRect();
        let top = rect.bottom + 20;
        let left = rect.left;

        // Keep tooltip in viewport
        if (top + tooltipRect.height > window.innerHeight) {
            top = rect.top - tooltipRect.height - 20;
        }
        if (left + tooltipRect.width > window.innerWidth) {
            left = window.innerWidth - tooltipRect.width - 20;
        }
        if (left < 20) {
            left = 20;
        }

        this.tooltip.style.top = `${top}px`;
        this.tooltip.style.left = `${left}px`;
        this.tooltip.style.display = 'block';
    }

    nextStep() {
        this.currentStep++;
        this.showStep(this.currentStep);
    }

    prevStep() {
        this.currentStep--;
        this.showStep(this.currentStep);
    }

    endTour() {
        if (this.overlay) this.overlay.classList.remove('active');
        if (this.spotlight) this.spotlight.style.display = 'none';
        if (this.tooltip) this.tooltip.style.display = 'none';
        if (this.welcomeModal) this.welcomeModal.remove();

        // Remove badges
        document.querySelectorAll('.tour-badge').forEach(badge => badge.remove());

        // Mark as completed for this hotel
        const hotelId = document.body.dataset.hotelId;
        if (hotelId) {
            localStorage.setItem(`housekeepr_tour_completed_hotel_${hotelId}`, 'true');
        }
        // Also set global flag for backwards compatibility
        localStorage.setItem('housekeepr_tour_completed', 'true');
    }

    addBadgesToButtons() {
        this.steps.forEach(step => {
            const button = document.querySelector(step.target);
            if (button && !button.querySelector('.tour-badge')) {
                const badge = document.createElement('span');
                badge.className = 'tour-badge large';
                badge.textContent = step.badge;

                // Make sure button is relatively positioned
                if (getComputedStyle(button).position === 'static') {
                    button.style.position = 'relative';
                }

                button.appendChild(badge);
            }
        });
    }

    // Method to reset the tour (useful for testing)
    static resetTour() {
        localStorage.removeItem('housekeepr_tour_completed');
        console.log('✅ Tour reset! Refresh the page to see the tour again.');
    }
}

// Initialize tour immediately and make globally accessible
window.housekeeprTour = null;

document.addEventListener('DOMContentLoaded', function() {
    // Check if tour was already completed (check hotel-specific first, then global)
    const hotelId = document.body.dataset.hotelId;
    let tourCompleted = false;

    if (hotelId) {
        tourCompleted = localStorage.getItem(`housekeepr_tour_completed_hotel_${hotelId}`) === 'true';
    }

    // Fallback to global check for backwards compatibility
    if (!tourCompleted) {
        tourCompleted = localStorage.getItem('housekeepr_tour_completed') === 'true';
    }

    if (!tourCompleted) {
        window.housekeeprTour = new HousekeeprTour();
        window.housekeeprTour.init();
    }
});

// Expose reset function globally for easy testing
window.resetTour = HousekeeprTour.resetTour;

