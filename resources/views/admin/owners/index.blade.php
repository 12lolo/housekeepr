@extends('layouts.app-neu')

@php
    $portalName = 'Admin';
@endphp

@section('page-title', 'Eigenaren')

@section('nav')
    <a href="{{ route('admin.dashboard') }}">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
        </svg>
        Dashboard
    </a>
    <a href="{{ route('admin.owners.index') }}" class="active">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
        </svg>
        Eigenaren
    </a>
    <a href="{{ route('admin.audit-log.index') }}">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 0l-2 2a1 1 0 101.414 1.414L8 10.414l1.293 1.293a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        Audit Log
    </a>
@endsection

@section('content')
    <div class="neu-widget">
        <div class="widget-header">
            <h2>Eigenaren Beheer</h2>
            <button type="button" class="neu-button-primary" id="openAddOwnerBtn" onclick="document.getElementById('addOwnerModal').classList.add('active'); document.body.style.overflow = 'hidden';">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" class="icon-inline">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                </svg>
                Nieuwe Eigenaar
            </button>
        </div>
        <div class="widget-body">
            @if($owners->count() > 0)
                <div class="neu-table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Naam</th>
                                <th>Email</th>
                                <th>Hotel</th>
                                <th>Status</th>
                                <th>Aangemaakt</th>
                                <th>Acties</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($owners as $owner)
                            <tr>
                                <td><strong>{{ $owner->name }}</strong></td>
                                <td>{{ $owner->email }}</td>
                                <td>{{ $owner->hotel->name ?? '-' }}</td>
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
                                <td>{{ $owner->created_at->format('d-m-Y') }}</td>
                                <td class="table-actions">
                                    <a href="{{ route('admin.owners.show', $owner) }}" class="action-btn" aria-label="Bekijken">
                                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.owners.edit', $owner) }}" class="action-btn" aria-label="Bewerken">
                                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                        </svg>
                                    </a>
                                    @if($owner->status === 'active')
                                        <form action="{{ route('admin.owners.deactivate', $owner) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="action-btn" style="color: #f59e0b;" aria-label="Deactiveren" onclick="return confirm('Weet je zeker dat je deze eigenaar wilt deactiveren? De eigenaar kan nog inloggen en navigeren, maar kan geen wijzigingen maken.')">
                                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.owners.activate', $owner) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="action-btn" style="color: #10b981;" aria-label="Activeren" onclick="return confirm('Weet je zeker dat je deze eigenaar wilt activeren?')">
                                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.owners.destroy', $owner) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn danger" aria-label="Verwijderen" onclick="return confirm('Weet je zeker dat je deze eigenaar definitief wilt verwijderen? Dit kan niet ongedaan worden gemaakt!')">
                                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $owners->links() }}
                </div>
            @else
                <div class="neu-empty-state">
                    <div class="empty-icon">
                        <svg width="48" height="48" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                        </svg>
                    </div>
                    <div class="empty-title">Geen eigenaren</div>
                    <div class="empty-message">Voeg een nieuwe eigenaar toe om te beginnen</div>
                </div>
            @endif
        </div>
    </div>

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
                <form action="{{ route('admin.owners.store') }}" method="POST" id="addOwnerForm">
                    @csrf

                    <div class="neu-form-group">
                        <label for="email" class="neu-label">Email Eigenaar</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="neu-input"
                            placeholder="eigenaar@hotel.nl"
                            required
                        >
                        <small class="neu-hint">Een uitnodigingsmail met tijdelijk wachtwoord wordt naar dit adres gestuurd. De eigenaar vult zelf zijn naam, wachtwoord en hotel in bij eerste login.</small>
                    </div>

                    <div class="neu-modal-footer">
                        <button type="button" class="neu-button-secondary" id="cancelAddOwnerBtn" onclick="document.getElementById('addOwnerModal').classList.remove('active'); document.body.style.overflow = ''; document.getElementById('addOwnerForm').reset();">Annuleren</button>
                        <button type="submit" class="neu-button-primary">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" class="icon-inline">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Eigenaar Aanmaken & Uitnodiging Versturen
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
        // Modal functionality
        const modal = document.getElementById('addOwnerModal');
        const openBtn = document.getElementById('openAddOwnerBtn');
        const closeBtn = document.getElementById('closeAddOwnerBtn');
        const cancelBtn = document.getElementById('cancelAddOwnerBtn');

        // Debug: Check if elements exist
        console.log('Modal elements:', {
            modal: !!modal,
            openBtn: !!openBtn,
            closeBtn: !!closeBtn,
            cancelBtn: !!cancelBtn
        });

        if (!modal || !openBtn) {
            console.error('Modal elements not found!');
            return;
        }

        function openModal() {
            console.log('Opening modal...');
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            console.log('Closing modal...');
            modal.classList.remove('active');
            document.body.style.overflow = '';
            const form = document.getElementById('addOwnerForm');
            if (form) form.reset();
        }

        openBtn.addEventListener('click', function(e) {
            e.preventDefault();
            openModal();
        });

        if (closeBtn) {
            closeBtn.addEventListener('click', closeModal);
        }

        if (cancelBtn) {
            cancelBtn.addEventListener('click', closeModal);
        }

        // Close on overlay click
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });

        // Close on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal.classList.contains('active')) {
                closeModal();
            }
        });
    });
</script>
@endpush

