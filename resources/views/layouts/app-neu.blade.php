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

                    <button class="notification-badge" aria-label="Notifications">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                        </svg>
                        @if(session('notification_count', 0) > 0)
                            <span class="badge-dot"></span>
                        @endif
                    </button>

                    <form method="POST" action="{{ route('logout') }}" style="display: inline;" id="logoutForm">
                        @csrf
                        <button type="button" class="notification-badge" aria-label="Logout" onclick="confirmLogout()">
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

    <script>
        // Mobile menu toggle
        document.getElementById('menuToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar')?.classList.toggle('open');
        });

        // Close alerts
        document.querySelectorAll('.alert-close')?.forEach(btn => {
            btn.addEventListener('click', function() {
                this.closest('.neu-alert')?.remove();
            });
        });

        // Logout confirmation
        function confirmLogout() {
            if (confirm('Weet je zeker dat je wilt uitloggen?')) {
                document.getElementById('logoutForm').submit();
            }
        }

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
    </script>

    @stack('scripts')
</body>
</html>
