<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'HouseKeepr') }} - @yield('title', 'Dashboard')</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body @isset($isNewHotel) data-new-hotel="{{ $isNewHotel ? 'true' : 'false' }}" @endisset @isset($stats) data-total-rooms="{{ $stats['total_rooms'] ?? 0 }}" @endisset>
    <div class="neu-portal">
        <!-- Sidebar Navigation -->
        <aside class="neu-portal-sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="{{ route('dashboard') }}" class="logo">HouseKeepr</a>
            </div>

            <nav class="sidebar-nav">
                @yield('nav')
            </nav>

            <div class="sidebar-footer">
                <button class="user-profile" id="openProfileBtn" type="button">
                    <div class="avatar">{{ auth()->user()->name ? strtoupper(substr(auth()->user()->name, 0, 1)) : strtoupper(substr(auth()->user()->email, 0, 1)) }}</div>
                    <div class="user-info">
                        <div class="name">{{ auth()->user()->name ?? auth()->user()->email }}</div>
                        <div class="role">{{ $portalName ?? 'User' }}</div>
                    </div>
                    <svg class="profile-chevron" width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </aside>

        <!-- Mobile sidebar backdrop -->
        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

        <!-- Main Content Area -->
        <main class="neu-portal-main">
            <!-- Top Navbar (Global Header) -->
            <nav class="neu-portal-navbar">
                <div class="navbar-left">
                    <button class="menu-toggle" id="menuToggle" aria-label="Toggle menu">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>

                <div class="navbar-right">
                    <form method="POST" action="{{ route('profile.toggle-notifications') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="notification-badge" aria-label="Toggle Email Notifications" title="{{ auth()->user()->notifications_enabled ? 'E-mail notificaties uitschakelen' : 'E-mail notificaties inschakelen' }}">
                            @if(auth()->user()->notifications_enabled)
                                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                </svg>
                            @else
                                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A2.014 2.014 0 0018 14V8.118l-8 4-1.473-.736-4.93-4.93C3.403 6.289 3.206 6.148 3 6.035V14a2 2 0 01-1 1.732l1.293 1.293 1.414-1.414L3.707 2.293z" clip-rule="evenodd"/>
                                    <path d="M3 5.884V14a2 2 0 002 2h6.586L3 7.414V5.884zM12.414 4L4 12.414V4h8.414z"/>
                                </svg>
                            @endif
                        </button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}" style="display: inline;" id="logoutForm">
                        @csrf
                        <button type="button" class="notification-badge" aria-label="Logout" id="logoutButton">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </nav>

            <!-- Content Area -->
            <div class="neu-portal-content">
                @if(session('success'))
                    <div class="neu-alert success">
                        <div class="alert-icon">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="alert-content">
                            <div class="alert-message">{{ session('success') }}</div>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="neu-alert danger">
                        <div class="alert-icon">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="alert-content">
                            <div class="alert-message">{{ session('error') }}</div>
                        </div>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <!-- Profile Panel Modal -->
    <div class="neu-modal-overlay" id="profileModal">
        <div class="neu-modal neu-modal-sm">
            <div class="neu-modal-header">
                <h3>Profiel</h3>
                <button class="neu-modal-close" id="closeProfileModal" type="button">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>

            <div class="neu-modal-body">
                <!-- User Info -->
                <div style="text-align: center; margin-bottom: 2rem;">
                    <div style="width: 80px; height: 80px; margin: 0 auto 1rem; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 700; color: white; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);">
                        {{ auth()->user()->name ? strtoupper(substr(auth()->user()->name, 0, 1)) : strtoupper(substr(auth()->user()->email, 0, 1)) }}
                    </div>
                    <h4 style="margin: 0 0 0.25rem 0; font-size: 1.25rem;">{{ auth()->user()->name ?? auth()->user()->email }}</h4>
                    <p style="margin: 0; color: var(--text-muted); font-size: 0.875rem;">{{ auth()->user()->email }}</p>
                </div>

                <!-- Profile Actions -->
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <button class="neu-button-secondary" id="openChangeNameBtn" type="button" style="width: 100%; justify-content: flex-start; gap: 0.75rem;">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                        <span>Naam wijzigen</span>
                    </button>

                    <button class="neu-button-secondary" id="openChangePasswordBtn" type="button" style="width: 100%; justify-content: flex-start; gap: 0.75rem;">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                        </svg>
                        <span>Wachtwoord wijzigen</span>
                    </button>

                    <form method="POST" action="{{ route('logout') }}" style="width: 100%;">
                        @csrf
                        <button type="submit" class="neu-button-secondary" style="width: 100%; justify-content: flex-start; gap: 0.75rem; color: #ef4444;">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"/>
                            </svg>
                            <span>Uitloggen</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div class="neu-modal-overlay" id="changePasswordModal">
        <div class="neu-modal neu-modal-sm">
            <div class="neu-modal-header">
                <h3>Wachtwoord wijzigen</h3>
                <button class="neu-modal-close" id="closeChangePasswordModal" type="button">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>

            <div class="neu-modal-body">
                <form method="POST" action="{{ route('password.update') }}" id="changePasswordForm">
                    @csrf
                    @method('PUT')

                    <div class="neu-form-group">
                        <label for="current_password" class="neu-label">Huidig wachtwoord</label>
                        <input type="password" id="current_password" name="current_password" class="neu-input" required>
                    </div>

                    <div class="neu-form-group">
                        <label for="password" class="neu-label">Nieuw wachtwoord</label>
                        <input type="password" id="password" name="password" class="neu-input" required minlength="8">
                        <small style="display: block; margin-top: 0.5rem; color: var(--text-muted); font-size: 0.875rem;">Minimaal 8 tekens</small>
                    </div>

                    <div class="neu-form-group">
                        <label for="password_confirmation" class="neu-label">Bevestig nieuw wachtwoord</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="neu-input" required>
                    </div>

                    <div class="neu-modal-footer" style="margin-top: 1.5rem; padding: 0; border: none;">
                        <button type="button" class="neu-button-secondary" id="cancelChangePassword">Annuleren</button>
                        <button type="submit" class="neu-button-primary">Opslaan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Change Name Modal -->
    <div class="neu-modal-overlay" id="changeNameModal">
        <div class="neu-modal neu-modal-sm">
            <div class="neu-modal-header">
                <h3>Naam wijzigen</h3>
                <button class="neu-modal-close" id="closeChangeNameModal" type="button">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>

            <div class="neu-modal-body">
                <form method="POST" action="{{ route('profile.update.name') }}" id="changeNameForm">
                    @csrf
                    @method('PUT')

                    <div class="neu-form-group">
                        <label for="name" class="neu-label">Naam</label>
                        <input type="text" id="name" name="name" class="neu-input" value="{{ auth()->user()->name }}" required maxlength="255">
                    </div>

                    <div class="neu-modal-footer" style="margin-top: 1.5rem; padding: 0; border: none;">
                        <button type="button" class="neu-button-secondary" id="cancelChangeName">Annuleren</button>
                        <button type="submit" class="neu-button-primary">Opslaan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Confirmation Modal --}}
    <div class="neu-modal-overlay" id="confirmModal" style="z-index: 10001;">
        <div class="neu-modal confirm-modal-compact">
            <div class="neu-modal-header confirm-modal-header">
                <h3 id="confirmTitle">Bevestiging</h3>
                <button class="neu-modal-close confirm-modal-close" type="button" onclick="closeConfirmModal()">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>

            <div class="neu-modal-body confirm-modal-body">
                <div style="display: flex; gap: 0.75rem; align-items: start;">
                    <div style="color: #f59e0b; flex-shrink: 0;">
                        <svg width="32" height="32" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div style="flex: 1;">
                        <p id="confirmMessage" style="margin: 0; line-height: 1.5; font-size: 0.9375rem; color: var(--neu-text-primary);"></p>
                    </div>
                </div>
            </div>

            <div class="neu-modal-footer confirm-modal-footer">
                <button type="button" class="neu-button-secondary" onclick="closeConfirmModal()">Annuleren</button>
                <button type="button" class="neu-button-danger" id="confirmButton" onclick="confirmAction()">Bevestigen</button>
            </div>
        </div>
    </div>

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
        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menuToggle');
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');

            function toggleSidebar() {
                sidebar.classList.toggle('open');
                backdrop.classList.toggle('active');
            }

            if (menuToggle && sidebar && backdrop) {
                menuToggle.addEventListener('click', toggleSidebar);
                backdrop.addEventListener('click', toggleSidebar);
            } else {
                console.error('Menu elements not found', { menuToggle, sidebar, backdrop });
            }
        });

        // Close alerts
        document.querySelectorAll('.alert-close')?.forEach(btn => {
            btn.addEventListener('click', function() {
                this.closest('.neu-alert')?.remove();
            });
        });

        // Profile Panel Modal
        const profileModal = document.getElementById('profileModal');
        const openProfileBtn = document.getElementById('openProfileBtn');
        const closeProfileModal = document.getElementById('closeProfileModal');

        openProfileBtn?.addEventListener('click', () => {
            profileModal.classList.add('active');
        });

        closeProfileModal?.addEventListener('click', () => {
            profileModal.classList.remove('active');
        });

        profileModal?.addEventListener('click', (e) => {
            if (e.target === profileModal) {
                profileModal.classList.remove('active');
            }
        });

        // Change Password Modal
        const changePasswordModal = document.getElementById('changePasswordModal');
        const openChangePasswordBtn = document.getElementById('openChangePasswordBtn');
        const closeChangePasswordModal = document.getElementById('closeChangePasswordModal');
        const cancelChangePassword = document.getElementById('cancelChangePassword');

        openChangePasswordBtn?.addEventListener('click', () => {
            profileModal.classList.remove('active');
            changePasswordModal.classList.add('active');
        });

        closeChangePasswordModal?.addEventListener('click', () => {
            changePasswordModal.classList.remove('active');
        });

        cancelChangePassword?.addEventListener('click', () => {
            changePasswordModal.classList.remove('active');
            profileModal.classList.add('active');
        });

        changePasswordModal?.addEventListener('click', (e) => {
            if (e.target === changePasswordModal) {
                changePasswordModal.classList.remove('active');
            }
        });

        // Change Name Modal
        const changeNameModal = document.getElementById('changeNameModal');
        const openChangeNameBtn = document.getElementById('openChangeNameBtn');
        const closeChangeNameModal = document.getElementById('closeChangeNameModal');
        const cancelChangeName = document.getElementById('cancelChangeName');

        openChangeNameBtn?.addEventListener('click', () => {
            profileModal.classList.remove('active');
            changeNameModal.classList.add('active');
        });

        closeChangeNameModal?.addEventListener('click', () => {
            changeNameModal.classList.remove('active');
        });

        cancelChangeName?.addEventListener('click', () => {
            changeNameModal.classList.remove('active');
            profileModal.classList.add('active');
        });

        changeNameModal?.addEventListener('click', (e) => {
            if (e.target === changeNameModal) {
                changeNameModal.classList.remove('active');
            }
        });

        // Confirmation Modal System
        let confirmCallback = null;

        function showConfirmModal(title, message, buttonText = 'Bevestigen', callback) {
            const modal = document.getElementById('confirmModal');
            const titleEl = document.getElementById('confirmTitle');
            const messageEl = document.getElementById('confirmMessage');
            const buttonEl = document.getElementById('confirmButton');

            if (!modal || !titleEl || !messageEl || !buttonEl) {
                console.error('Modal elements not found!');
                return;
            }

            titleEl.textContent = title;
            messageEl.textContent = message;
            buttonEl.textContent = buttonText;
            confirmCallback = callback;

            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeConfirmModal() {
            const modal = document.getElementById('confirmModal');
            modal.classList.remove('active');
            document.body.style.overflow = '';
            confirmCallback = null;
        }

        function confirmAction() {
            if (confirmCallback) {
                confirmCallback();
            }
            closeConfirmModal();
        }

        // Close modal on ESC key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const confirmModal = document.getElementById('confirmModal');
                if (confirmModal?.classList.contains('active')) {
                    closeConfirmModal();
                }
            }
        });

        // Close modal on overlay click
        document.getElementById('confirmModal')?.addEventListener('click', (e) => {
            if (e.target === e.currentTarget) {
                closeConfirmModal();
            }
        });

        // Logout confirmation - attach event listener
        const logoutButton = document.getElementById('logoutButton');
        if (logoutButton) {
            logoutButton.addEventListener('click', function() {
                showConfirmModal(
                    'Uitloggen',
                    'Weet je zeker dat je wilt uitloggen?',
                    'Uitloggen',
                    () => document.getElementById('logoutForm').submit()
                );
            });
        }
    </script>

    @stack('scripts')
</body>
</html>
