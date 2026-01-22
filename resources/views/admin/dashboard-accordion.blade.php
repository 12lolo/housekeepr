@extends('layouts.app-neu')

@section('title', 'Admin Dashboard')

@php
    $portalName = 'Admin Portal';
@endphp

@section('nav')
    <a href="javascript:void(0)" class="nav-section-link active" data-section="dashboard" onclick="event.preventDefault();">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
        </svg>
        Dashboard
    </a>
    <a href="javascript:void(0)" class="nav-section-link" data-section="eigenaren" onclick="event.preventDefault();">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
        </svg>
        Eigenaren
    </a>
    <a href="javascript:void(0)" class="nav-section-link" data-section="audit-log" onclick="event.preventDefault();">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
        </svg>
        Audit Log
    </a>
@endsection

@section('content')
<div class="neu-accordion-container">
    <!-- Dashboard Section (Open by default) -->
    <div class="neu-accordion-section" id="section-dashboard">
        <button class="neu-accordion-header active" data-section="dashboard">
            <div class="neu-accordion-header-content">
                <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                </svg>
                <span>Dashboard</span>
            </div>
            <svg class="neu-accordion-icon" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
        </button>

        <div class="neu-accordion-content active">
            <!-- Dashboard Stats Grid -->
            <div class="neu-dashboard-grid">
                <!-- Total Hotels -->
                <div class="neu-stat-card">
                    <div class="stat-icon">
                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                        </svg>
                    </div>
                    <div class="stat-label">Totaal Hotels</div>
                    <div class="stat-value">{{ $stats['total_hotels'] }}</div>
                </div>

                <!-- Total Owners -->
                <div class="neu-stat-card">
                    <div class="stat-icon">
                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                        </svg>
                    </div>
                    <div class="stat-label">Totaal Eigenaren</div>
                    <div class="stat-value">{{ $stats['total_owners'] }}</div>
                </div>

                <!-- Total Cleaners -->
                <div class="neu-stat-card">
                    <div class="stat-icon">
                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="stat-label">Totaal Schoonmakers</div>
                    <div class="stat-value">{{ $stats['total_cleaners'] }}</div>
                </div>

                <!-- Pending Approvals -->
                <div class="neu-stat-card highlight">
                    <div class="stat-icon">
                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="stat-label">Wachtend op Goedkeuring</div>
                    <div class="stat-value">{{ $stats['pending_approvals'] }}</div>
                </div>
            </div>

            <!-- Recent Hotels Table -->
            <div class="neu-card mt-6">
                <h2 class="card-title">Recente Hotels</h2>
                <div class="table-container">
                    <table class="neu-table">
                        <thead>
                            <tr>
                                <th>Hotel Naam</th>
                                <th>Eigenaar</th>
                                <th>Locatie</th>
                                <th>Aangemaakt</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentHotels as $hotel)
                                <tr>
                                    <td><strong>{{ $hotel->name }}</strong></td>
                                    <td>{{ $hotel->owner->name }}</td>
                                    <td>{{ $hotel->address }}</td>
                                    <td>{{ $hotel->created_at->format('d-m-Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Geen hotels gevonden</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Eigenaren Section -->
    <div class="neu-accordion-section" id="section-eigenaren">
        <button class="neu-accordion-header" data-section="eigenaren">
            <div class="neu-accordion-header-content">
                <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                </svg>
                <span>Eigenaren</span>
            </div>
            <svg class="neu-accordion-icon" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
        </button>

        <div class="neu-accordion-content">
            <div class="neu-card">
                <div class="card-header-actions">
                    <h2 class="card-title">Alle Eigenaren</h2>
                    <button type="button" class="neu-button-primary" id="openAddOwnerBtn" onclick="document.getElementById('addOwnerModal').classList.add('active'); document.body.style.overflow = 'hidden';">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                        </svg>
                        Nieuwe Eigenaar
                    </button>
                </div>

                <div class="table-container">
                    <table class="neu-table">
                        <thead>
                            <tr>
                                <th>Naam</th>
                                <th>Email</th>
                                <th>Hotels</th>
                                <th>Status</th>
                                <th>Acties</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($owners as $owner)
                                <tr>
                                    <td><strong>{{ $owner->name }}</strong></td>
                                    <td>{{ $owner->email }}</td>
                                    <td>{{ $owner->hotels_count ?? 0 }}</td>
                                    <td>
                                        @if($owner->status === 'active')
                                            <span class="neu-badge success">Actief</span>
                                        @elseif($owner->status === 'pending')
                                            <span class="neu-badge warning">Pending</span>
                                        @elseif($owner->status === 'deactivated')
                                            <span class="neu-badge danger">Gedeactiveerd</span>
                                        @else
                                            <span class="neu-badge">{{ ucfirst($owner->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-buttons" style="display: flex; gap: 0.5rem;">
                                            @if($owner->status === 'active')
                                                <form action="{{ route('admin.owners.deactivate', $owner) }}" method="POST" class="d-inline" data-owner-form="deactivate">
                                                    @csrf
                                                    <button type="button" class="action-btn" style="color: #f59e0b;" aria-label="Deactiveren" onclick="confirmDeactivateOwner(event, '{{ $owner->name ?? $owner->email }}')">
                                                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.owners.activate', $owner) }}" method="POST" class="d-inline" data-owner-form="activate">
                                                    @csrf
                                                    <button type="button" class="action-btn" style="color: #10b981;" aria-label="Activeren" onclick="confirmActivateOwner(event, '{{ $owner->name ?? $owner->email }}')">
                                                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('admin.owners.destroy', $owner) }}" method="POST" class="d-inline" data-owner-form="delete">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="action-btn danger" aria-label="Verwijderen" onclick="confirmDeleteOwner(event, '{{ $owner->name ?? $owner->email }}')">
                                                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Geen eigenaren gevonden</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Audit Log Section -->
    <div class="neu-accordion-section" id="section-audit-log">
        <button class="neu-accordion-header" data-section="audit-log">
            <div class="neu-accordion-header-content">
                <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                </svg>
                <span>Audit Log</span>
            </div>
            <svg class="neu-accordion-icon" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
        </button>

        <div class="neu-accordion-content">
            <div class="neu-card">
                <h2 class="card-title">Audit Log - Alle Activiteiten</h2>

                {{-- Audit Log Filters --}}
                <form method="GET" action="{{ route('admin.dashboard') }}" id="auditLogFilterForm" class="audit-log-filters" style="margin-bottom: 2rem;" onsubmit="filterAuditLog(event)">
                    <div class="filter-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
                        <div class="neu-form-group">
                            <label for="user_id" class="neu-label">Gebruiker</label>
                            <select id="user_id" name="user_id" class="neu-input">
                                <option value="">Alle gebruikers</option>
                                @foreach($causers as $causer)
                                    <option value="{{ $causer->id }}" {{ request('user_id') == $causer->id ? 'selected' : '' }}>
                                        {{ $causer->name ?? $causer->email }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="neu-form-group">
                            <label for="event" class="neu-label">Type Actie</label>
                            <select id="event" name="event" class="neu-input">
                                <option value="">Alle acties</option>
                                <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>Aangemaakt</option>
                                <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>Bijgewerkt</option>
                                <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>Verwijderd</option>
                            </select>
                        </div>

                        <div class="neu-form-group">
                            <label for="from_date" class="neu-label">Van Datum</label>
                            <input
                                type="date"
                                id="from_date"
                                name="from_date"
                                class="neu-input"
                                value="{{ request('from_date') }}"
                            >
                        </div>

                        <div class="neu-form-group">
                            <label for="to_date" class="neu-label">Tot Datum</label>
                            <input
                                type="date"
                                id="to_date"
                                name="to_date"
                                class="neu-input"
                                value="{{ request('to_date') }}"
                            >
                        </div>

                        <div class="neu-form-group">
                            <label for="search" class="neu-label">Zoeken</label>
                            <input
                                type="text"
                                id="search"
                                name="search"
                                class="neu-input"
                                placeholder="Zoek in beschrijving..."
                                value="{{ request('search') }}"
                            >
                        </div>
                    </div>

                    <div class="filter-actions" style="display: flex; gap: 0.75rem;">
                        <button type="submit" class="neu-button-primary">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" class="icon-inline">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                            </svg>
                            Filteren
                        </button>
                        <button type="button" id="clearFiltersBtn" class="neu-button-secondary" onclick="clearAuditLogFilters()" style="display: none;">
                            Filters Wissen
                        </button>
                    </div>
                </form>

                <div class="table-container">
                    <table class="neu-table">
                        <thead>
                            <tr>
                                <th>Gebruiker</th>
                                <th>Actie</th>
                                <th>Details</th>
                                <th>Tijdstip</th>
                            </tr>
                        </thead>
                        <tbody id="auditLogTableBody">
                            @forelse($auditLogs as $log)
                                <tr>
                                    <td><strong>{{ $log->causer->name ?? $log->causer->email ?? 'Systeem' }}</strong></td>
                                    <td>
                                        @if($log->event === 'created')
                                            <span class="neu-badge success">Aangemaakt</span>
                                        @elseif($log->event === 'updated')
                                            <span class="neu-badge warning">Bijgewerkt</span>
                                        @elseif($log->event === 'deleted')
                                            <span class="neu-badge danger">Verwijderd</span>
                                        @else
                                            <span class="neu-badge" style="background-color: #e5e7eb; color: #6b7280;">Actie</span>
                                        @endif
                                    </td>
                                    <td>{{ $log->description }}</td>
                                    <td>{{ $log->created_at->format('d-m-Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Geen audit logs gevonden</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination (AJAX) --}}
                <div id="auditLogPagination" class="mt-6" style="display: none;"></div>
            </div>
        </div>
    </div>
</div>

{{-- Confirmation Modal is provided by app-neu.blade.php layout --}}

<style>
/* Danger button style */
.neu-button-danger {
    background: #ef4444;
    color: white;
    border: none;
    padding: 0.625rem 1.25rem;
    border-radius: 8px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
}

.neu-button-danger:hover {
    background: #dc2626;
}

.neu-button-danger:active {
    background: #b91c1c;
}

/* Compact confirmation modal styles */
.confirm-modal-compact {
    max-width: 450px;
}

.confirm-modal-header {
    padding: 1rem 1.5rem;
}

.confirm-modal-body {
    padding: 1rem 1.5rem;
}

.confirm-modal-footer {
    padding: 1rem 1.5rem;
    gap: 0.75rem;
}

/* Dark mode compatible close button hover */
.confirm-modal-close:hover {
    opacity: 0.7;
    background: transparent !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const accordionHeaders = document.querySelectorAll('.neu-accordion-header');
    const navSectionLinks = document.querySelectorAll('.nav-section-link');

    // Accordion header click handler
    accordionHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const section = this.closest('.neu-accordion-section');
            const content = section.querySelector('.neu-accordion-content');
            const isActive = this.classList.contains('active');
            const sectionName = this.getAttribute('data-section');

            // Close all sections
            document.querySelectorAll('.neu-accordion-header').forEach(h => {
                h.classList.remove('active');
            });
            document.querySelectorAll('.neu-accordion-content').forEach(c => {
                c.classList.remove('active');
            });

            // Update sidebar nav active state
            document.querySelectorAll('.nav-section-link').forEach(link => {
                link.classList.remove('active');
            });

            // If wasn't active, open it and scroll to top
            if (!isActive) {
                this.classList.add('active');
                content.classList.add('active');

                // Set corresponding sidebar link as active
                const correspondingNavLink = document.querySelector(`.nav-section-link[data-section="${sectionName}"]`);
                if (correspondingNavLink) {
                    correspondingNavLink.classList.add('active');
                }

                setTimeout(() => {
                    section.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }, 100);
            }
        });
    });

    // Sidebar navigation link click handler
    navSectionLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const sectionName = this.getAttribute('data-section');

            // Find and click the corresponding accordion header
            const targetHeader = document.querySelector(`.neu-accordion-header[data-section="${sectionName}"]`);
            if (targetHeader) {
                targetHeader.click();
            }
        });
    });

    // Confirmation Modal System is provided by app-neu.blade.php layout
    // Owner management confirmation helper functions
    window.confirmDeactivateOwner = function(event, ownerName) {
        event.preventDefault();
        const form = event.target.closest('form');
        const parts = form.action.split('/');
        const ownerId = parts[parts.length - 2]; // Get ID from second-to-last position
        showConfirmModal(
            'Eigenaar deactiveren',
            `Weet je zeker dat je ${ownerName} wilt deactiveren? De eigenaar kan nog inloggen en navigeren, maar kan geen wijzigingen maken.`,
            'Deactiveren',
            () => deactivateOwner(form, ownerId, ownerName)
        );
    };

    window.deactivateOwner = function(form, ownerId, ownerName) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw { status: response.status, data: data };
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');

                // Update status badge
                const row = form.closest('tr');
                const statusCell = row.querySelector('td:nth-child(4)');
                statusCell.innerHTML = '<span class="neu-badge danger">Gedeactiveerd</span>';

                // Replace deactivate button with activate button
                const actionsCell = row.querySelector('td:nth-child(5) .action-buttons');
                const newForm = document.createElement('form');
                newForm.action = `/admin/owners/${ownerId}/activate`;
                newForm.method = 'POST';
                newForm.className = 'd-inline';
                newForm.setAttribute('data-owner-form', 'activate');
                newForm.innerHTML = `
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <button type="button" class="action-btn" style="color: #10b981;" onclick="confirmActivateOwner(event, '${ownerName}')">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                `;
                form.replaceWith(newForm);

                // Refresh audit log
                refreshAuditLog();
            } else {
                showToast(data.message, 'error', 5000);
            }
        })
        .catch(error => {
            console.error('Deactivate error:', error);
            showToast(error.data?.message || 'Er is een fout opgetreden.', 'error', 5000);
        });
    };

    window.confirmActivateOwner = function(event, ownerName) {
        event.preventDefault();
        const form = event.target.closest('form');
        const parts = form.action.split('/');
        const ownerId = parts[parts.length - 2]; // Get ID from second-to-last position
        showConfirmModal(
            'Eigenaar activeren',
            `Weet je zeker dat je ${ownerName} wilt activeren?`,
            'Activeren',
            () => activateOwner(form, ownerId, ownerName)
        );
    };

    window.activateOwner = function(form, ownerId, ownerName) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw { status: response.status, data: data };
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');

                // Update status badge
                const row = form.closest('tr');
                const statusCell = row.querySelector('td:nth-child(4)');
                statusCell.innerHTML = '<span class="neu-badge success">Actief</span>';

                // Replace activate button with deactivate button
                const actionsCell = row.querySelector('td:nth-child(5) .action-buttons');
                const newForm = document.createElement('form');
                newForm.action = `/admin/owners/${ownerId}/deactivate`;
                newForm.method = 'POST';
                newForm.className = 'd-inline';
                newForm.setAttribute('data-owner-form', 'deactivate');
                newForm.innerHTML = `
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <button type="button" class="action-btn" style="color: #f59e0b;" onclick="confirmDeactivateOwner(event, '${ownerName}')">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                `;
                form.replaceWith(newForm);

                // Refresh audit log
                refreshAuditLog();
            } else {
                showToast(data.message, 'error', 5000);
            }
        })
        .catch(error => {
            console.error('Activate error:', error);
            showToast(error.data?.message || 'Er is een fout opgetreden.', 'error', 5000);
        });
    };

    window.confirmDeleteOwner = function(event, ownerName) {
        event.preventDefault();
        const form = event.target.closest('form');
        const ownerId = form.action.split('/').pop();
        showConfirmModal(
            'Eigenaar verwijderen',
            `Weet je zeker dat je ${ownerName} definitief wilt verwijderen? Dit kan niet ongedaan worden gemaakt!`,
            'Verwijderen',
            () => deleteOwner(form, ownerId)
        );
    };

    window.deleteOwner = function(form, ownerId) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        fetch(form.action, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw { status: response.status, data: data };
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');

                // Fade out and remove row
                const row = form.closest('tr');
                row.style.opacity = '0';
                row.style.transition = 'opacity 0.3s ease';
                setTimeout(() => {
                    row.remove();

                    // Check if table is empty
                    const tbody = document.querySelector('#section-eigenaren tbody');
                    if (tbody && tbody.querySelectorAll('tr').length === 0) {
                        tbody.innerHTML = '<tr><td colspan="5" class="text-center">Geen eigenaren gevonden</td></tr>';
                    }

                    // Refresh audit log
                    refreshAuditLog();
                }, 300);
            } else {
                showToast(data.message, 'error', 5000);
            }
        })
        .catch(error => {
            console.error('Delete error:', error);
            showToast('Er is een fout opgetreden.', 'error', 5000);
        });
    };

    // Modal event listeners (ESC and overlay click) are provided by app-neu.blade.php layout

    // Check if any filters are active
    window.checkFiltersActive = function() {
        const form = document.getElementById('auditLogFilterForm');
        const formData = new FormData(form);
        let hasFilters = false;

        for (const [key, value] of formData.entries()) {
            if (value && value.trim() !== '') {
                hasFilters = true;
                break;
            }
        }

        const clearBtn = document.getElementById('clearFiltersBtn');
        if (clearBtn) {
            clearBtn.style.display = hasFilters ? 'inline-flex' : 'none';
        }
    };

    // Filter audit log (form submission)
    window.filterAuditLog = function(event) {
        event.preventDefault();
        checkFiltersActive();
        refreshAuditLog();
    };

    // Clear audit log filters
    window.clearAuditLogFilters = function() {
        const form = document.getElementById('auditLogFilterForm');
        form.reset();
        checkFiltersActive();
        refreshAuditLog();
    };

    // Check on page load if filters are active
    document.addEventListener('DOMContentLoaded', function() {
        checkFiltersActive();
    });

    // Change audit log page
    window.changeAuditLogPage = function(page) {
        const form = document.getElementById('auditLogFilterForm');
        const pageInput = form.querySelector('input[name="page"]');

        if (pageInput) {
            pageInput.value = page;
        } else {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'page';
            input.value = page;
            form.appendChild(input);
        }

        refreshAuditLog();
    };

    // Refresh audit log with cooldown to prevent excessive requests
    let auditLogCooldown = false;
    let auditLogPendingRefresh = false;

    window.refreshAuditLog = function() {
        const tbody = document.getElementById('auditLogTableBody');
        if (!tbody) return;

        // If on cooldown, mark that a refresh is pending
        if (auditLogCooldown) {
            auditLogPendingRefresh = true;
            return;
        }

        // Set cooldown (3 seconds)
        auditLogCooldown = true;
        setTimeout(() => {
            auditLogCooldown = false;
            // If a refresh was requested during cooldown, execute it now
            if (auditLogPendingRefresh) {
                auditLogPendingRefresh = false;
                refreshAuditLog();
            }
        }, 3000);

        // Get filter values from form
        const form = document.getElementById('auditLogFilterForm');
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);

        fetch('/admin/audit-logs?' + params.toString(), {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.logs) {
                // Clear existing rows
                tbody.innerHTML = '';

                if (data.logs.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center">Geen audit logs gevonden</td></tr>';
                    return;
                }

                // Add new rows
                data.logs.forEach(log => {
                    const row = document.createElement('tr');

                    let badgeHtml = '';
                    if (log.event === 'created') {
                        badgeHtml = '<span class="neu-badge success">Aangemaakt</span>';
                    } else if (log.event === 'updated') {
                        badgeHtml = '<span class="neu-badge warning">Bijgewerkt</span>';
                    } else if (log.event === 'deleted') {
                        badgeHtml = '<span class="neu-badge danger">Verwijderd</span>';
                    } else {
                        badgeHtml = '<span class="neu-badge" style="background-color: #e5e7eb; color: #6b7280;">Actie</span>';
                    }

                    row.innerHTML = `
                        <td><strong>${log.causer_name}</strong></td>
                        <td>${badgeHtml}</td>
                        <td>${log.description}</td>
                        <td>${log.created_at}</td>
                    `;

                    tbody.appendChild(row);
                });

                // Render pagination
                const paginationDiv = document.getElementById('auditLogPagination');
                if (data.pagination && data.pagination.last_page > 1) {
                    paginationDiv.style.display = 'block';
                    paginationDiv.innerHTML = renderPagination(data.pagination);
                } else {
                    paginationDiv.style.display = 'none';
                }
            }
        })
        .catch(error => {
            console.error('Error refreshing audit log:', error);
        });
    };

    // Render pagination HTML
    window.renderPagination = function(pagination) {
        const currentPage = pagination.current_page;
        const lastPage = pagination.last_page;
        const from = pagination.from;
        const to = pagination.to;
        const total = pagination.total;

        let html = '<nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">';

        // Results info
        html += '<div class="flex justify-between flex-1 sm:hidden">';
        if (currentPage > 1) {
            html += `<button onclick="changeAuditLogPage(${currentPage - 1})" class="neu-button-secondary">Vorige</button>`;
        } else {
            html += '<span></span>';
        }
        if (currentPage < lastPage) {
            html += `<button onclick="changeAuditLogPage(${currentPage + 1})" class="neu-button-secondary">Volgende</button>`;
        }
        html += '</div>';

        // Desktop pagination
        html += '<div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">';
        html += `<div><p class="text-sm text-gray-700 dark:text-gray-300">Resultaten <span class="font-medium">${from}</span> tot <span class="font-medium">${to}</span> van <span class="font-medium">${total}</span></p></div>`;
        html += '<div><span class="relative z-0 inline-flex shadow-sm rounded-md">';

        // Previous button
        if (currentPage > 1) {
            html += `<button onclick="changeAuditLogPage(${currentPage - 1})" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
            </button>`;
        } else {
            html += `<span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-300 dark:text-gray-600 cursor-not-allowed">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
            </span>`;
        }

        // Page numbers
        const startPage = Math.max(1, currentPage - 2);
        const endPage = Math.min(lastPage, currentPage + 2);

        if (startPage > 1) {
            html += `<button onclick="changeAuditLogPage(1)" class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">1</button>`;
            if (startPage > 2) {
                html += `<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-700 dark:text-gray-300">...</span>`;
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            if (i === currentPage) {
                html += `<span class="relative inline-flex items-center px-4 py-2 border border-blue-500 bg-blue-50 dark:bg-blue-900 text-sm font-medium text-blue-600 dark:text-blue-200">${i}</span>`;
            } else {
                html += `<button onclick="changeAuditLogPage(${i})" class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">${i}</button>`;
            }
        }

        if (endPage < lastPage) {
            if (endPage < lastPage - 1) {
                html += `<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-700 dark:text-gray-300">...</span>`;
            }
            html += `<button onclick="changeAuditLogPage(${lastPage})" class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">${lastPage}</button>`;
        }

        // Next button
        if (currentPage < lastPage) {
            html += `<button onclick="changeAuditLogPage(${currentPage + 1})" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
            </button>`;
        } else {
            html += `<span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-300 dark:text-gray-600 cursor-not-allowed">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
            </span>`;
        }

        html += '</span></div></div></nav>';
        return html;
    };

    // Toast notification system
    window.showToast = function(message, type = 'success', duration = 3000) {
        // Remove existing toasts
        const existing = document.querySelector('.toast-notification');
        if (existing) {
            existing.remove();
        }

        // Create toast element
        const toast = document.createElement('div');
        toast.className = `toast-notification toast-${type}`;
        toast.innerHTML = `
            <div class="toast-content">
                <span class="toast-icon">
                    ${type === 'success' ? '✓' : type === 'error' ? '✕' : 'i'}
                </span>
                <span class="toast-message">${message}</span>
            </div>
        `;

        // Add to body
        document.body.appendChild(toast);

        // Animate in
        setTimeout(() => toast.classList.add('show'), 10);

        // Remove after duration
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, duration);
    };

    // Owner create form submission
    window.submitOwnerCreateForm = function(event) {
        event.preventDefault();

        const form = event.target;
        const submitBtn = document.getElementById('ownerSubmitBtn');
        const cancelBtn = document.getElementById('cancelAddOwnerBtn');
        const formData = new FormData(form);

        // Disable buttons
        submitBtn.disabled = true;
        cancelBtn.disabled = true;
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" class="icon-inline animate-spin"><path fill-rule="evenodd" d="M4 2a2 2 0 00-2 2v11a3 3 0 106 0V4a2 2 0 00-2-2H4z" clip-rule="evenodd"/></svg> Bezig met versturen...';

        // Clear previous errors
        document.querySelectorAll('.error-text').forEach(el => {
            el.style.display = 'none';
            el.textContent = '';
        });
        document.querySelectorAll('.has-error').forEach(el => el.classList.remove('has-error'));

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
                    throw { status: response.status, data: data };
                });
            }
            return response.json();
        })
        .then(data => {
            showToast(data.message || 'Uitnodiging verstuurd!', 'success');

            // Close modal
            document.getElementById('addOwnerModal').classList.remove('active');
            document.body.style.overflow = '';
            form.reset();

            // Add new owner to the table without reload
            const tbody = document.querySelector('#section-eigenaren tbody');
            if (tbody && data.owner) {
                // Remove "no owners" placeholder if exists
                const noOwnersRow = tbody.querySelector('td[colspan="5"]');
                if (noOwnersRow) {
                    noOwnersRow.parentElement.remove();
                }

                // Create new row
                const newRow = document.createElement('tr');
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                const ownerName = data.owner.name || 'Pending';
                const ownerEmail = data.owner.email;
                const hotelsCount = data.owner.hotels_count || 0;

                newRow.innerHTML = `
                    <td><strong>${ownerName}</strong></td>
                    <td>${ownerEmail}</td>
                    <td>${hotelsCount}</td>
                    <td><span class="neu-badge warning">Pending</span></td>
                    <td>
                        <div class="action-buttons" style="display: flex; gap: 0.5rem;">
                            <form action="/admin/owners/${data.owner.id}/activate" method="POST" class="d-inline" data-owner-form="activate">
                                <input type="hidden" name="_token" value="${csrfToken}">
                                <button type="button" class="action-btn" style="color: #10b981;" aria-label="Activeren" onclick="confirmActivateOwner(event, '${ownerEmail}')">
                                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </form>
                            <form action="/admin/owners/${data.owner.id}" method="POST" class="d-inline" data-owner-form="delete">
                                <input type="hidden" name="_token" value="${csrfToken}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="button" class="action-btn danger" aria-label="Verwijderen" onclick="confirmDeleteOwner(event, '${ownerEmail}')">
                                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                `;

                // Add to top of table with fade-in animation
                newRow.style.opacity = '0';
                tbody.insertBefore(newRow, tbody.firstChild);
                setTimeout(() => {
                    newRow.style.transition = 'opacity 0.3s ease';
                    newRow.style.opacity = '1';
                }, 10);
            }

            // Refresh audit log
            refreshAuditLog();
        })
        .catch(error => {
            console.error('Owner create error:', error);

            // Handle validation errors (422)
            if (error.status === 422 && error.data.errors) {
                Object.keys(error.data.errors).forEach(field => {
                    const errorSpan = document.getElementById(`error_${field}`);
                    const inputField = document.querySelector(`[name="${field}"]`);

                    if (errorSpan && inputField) {
                        errorSpan.textContent = error.data.errors[field][0];
                        errorSpan.style.display = 'block';
                        inputField.classList.add('has-error');
                    }
                });
                showToast('Controleer de formuliervelden', 'error', 5000);
            } else {
                showToast(error.data?.message || 'Er is een fout opgetreden.', 'error', 5000);
            }
        })
        .finally(() => {
            // Re-enable buttons
            submitBtn.disabled = false;
            cancelBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    };
});
</script>

{{-- Add Owner Modal --}}
<div class="neu-modal-overlay" id="addOwnerModal">
    <div class="neu-modal">
        <div class="neu-modal-header">
            <h3>Nieuwe Eigenaar Uitnodigen</h3>
            <button type="button" class="neu-modal-close" id="closeAddOwnerBtn" aria-label="Sluiten" onclick="document.getElementById('addOwnerModal').classList.remove('active'); document.body.style.overflow = '';">
                <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
        <div class="neu-modal-body">
            <form action="{{ route('admin.owners.store') }}" method="POST" id="addOwnerForm" onsubmit="submitOwnerCreateForm(event)">
                @csrf

                <div class="neu-form-group">
                    <label for="owner_email" class="neu-label">Email Adres <span style="color: #e53e3e;">*</span></label>
                    <input
                        type="email"
                        id="owner_email"
                        name="email"
                        class="neu-input"
                        placeholder="eigenaar@hotel.nl"
                        required
                        autofocus
                    >
                    <span id="error_email" class="error-text" style="display: none; color: #e53e3e; font-size: 0.875rem; margin-top: 0.25rem;"></span>
                    <small class="neu-hint">De hotel eigenaar ontvangt een uitnodigingsmail en kan dan zelf alle gegevens invullen (naam, hotel, etc.).</small>
                </div>

                <div class="neu-modal-footer">
                    <button type="button" class="neu-button-secondary" id="cancelAddOwnerBtn" onclick="document.getElementById('addOwnerModal').classList.remove('active'); document.body.style.overflow = ''; document.getElementById('addOwnerForm').reset();">Annuleren</button>
                    <button type="submit" class="neu-button-primary" id="ownerSubmitBtn">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" class="icon-inline">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Uitnodiging Versturen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // If audit log filters are applied, automatically open the audit log section
    @if(request()->hasAny(['user_id', 'event', 'from_date', 'to_date', 'search']))
        const auditLogHeader = document.querySelector('.neu-accordion-header[data-section="audit-log"]');
        if (auditLogHeader) {
            auditLogHeader.click();
            setTimeout(() => {
                auditLogHeader.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 200);
        }
    @endif
});
</script>
@endpush

