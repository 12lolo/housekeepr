@extends('layouts.app-neu')

@php
    $portalName = 'Owner';
@endphp

@section('page-title', 'Account Instellen')

@section('nav')
    <a href="{{ route('owner.dashboard') }}" class="active">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
        </svg>
        Dashboard
    </a>
@endsection

@section('content')
    <div class="neu-widget" style="max-width: 600px; margin: 0 auto;">
        <div class="widget-header" style="text-align: center; flex-direction: column; gap: 1rem;">
            <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                <svg width="40" height="40" fill="white" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                </svg>
            </div>
            <h2 style="margin: 0;">Welkom bij HouseKeepr!</h2>
            <p style="margin: 0; color: #6b7280; font-size: 1rem;">Stel eerst je account in</p>
        </div>

        <div class="widget-body" style="padding: 2rem;">
            <!-- Progress Steps -->
            <div style="display: flex; justify-content: center; align-items: center; margin-bottom: 2rem; gap: 0.5rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <div style="width: 32px; height: 32px; background: #667eea; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px;">1</div>
                    <span style="font-weight: 600; color: #667eea;">Account</span>
                </div>
                <svg width="20" height="20" fill="#d1d5db" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <div style="width: 32px; height: 32px; background: #e5e7eb; color: #9ca3af; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px;">2</div>
                    <span style="color: #9ca3af;">Hotel</span>
                </div>
            </div>

            <!-- Welcome Message -->
            <div style="background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 1px solid #667eea30;">
                <p style="margin: 0; color: #4b5563; line-height: 1.6;">
                    👋 Je bent uitgenodigd om HouseKeepr te gebruiken. Vul hieronder je naam in en wijzig optioneel je wachtwoord.
                </p>
            </div>

            <!-- Account Setup Form -->
            <form method="POST" action="{{ route('owner.setup.account.store') }}" id="accountSetupForm">
                @csrf

                <!-- Name -->
                <div class="neu-form-group">
                    <label for="name">Naam *</label>
                    <input type="text"
                           id="name"
                           name="name"
                           value="{{ old('name', auth()->user()->name) }}"
                           required
                           autofocus
                           placeholder="Vul je naam in">
                    @error('name')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Current Password (optional) -->
                <div class="neu-form-group">
                    <label for="current_password">Huidig Wachtwoord (optioneel)</label>
                    <input type="password"
                           id="current_password"
                           name="current_password"
                           placeholder="Laat leeg om wachtwoord niet te wijzigen">
                    <small style="color: #6b7280; font-size: 0.875rem; display: block; margin-top: 0.25rem;">
                        Vul alleen in als je je wachtwoord wilt wijzigen
                    </small>
                    @error('current_password')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- New Password (optional) -->
                <div class="neu-form-group">
                    <label for="password">Nieuw Wachtwoord (optioneel)</label>
                    <input type="password"
                           id="password"
                           name="password"
                           placeholder="Kies een nieuw wachtwoord">
                    <small style="color: #6b7280; font-size: 0.875rem; display: block; margin-top: 0.25rem;">
                        Minimaal 8 karakters
                    </small>
                    @error('password')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Confirm Password (optional) -->
                <div class="neu-form-group">
                    <label for="password_confirmation">Bevestig Nieuw Wachtwoord</label>
                    <input type="password"
                           id="password_confirmation"
                           name="password_confirmation"
                           placeholder="Bevestig je nieuwe wachtwoord">
                    @error('password_confirmation')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Info Box -->
                <div style="background: #fef3c7; border: 1px solid #fbbf24; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    <div style="display: flex; gap: 0.75rem;">
                        <svg style="flex-shrink: 0; color: #f59e0b;" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div style="font-size: 0.875rem; color: #92400e;">
                            <strong>Aanbeveling:</strong> We raden sterk aan om je wachtwoord te wijzigen voor extra beveiliging.
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="neu-button-primary" style="width: 100%;">
                    Volgende: Hotel Instellen →
                </button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('accountSetupForm');
            const currentPassword = document.getElementById('current_password');
            const password = document.getElementById('password');
            const passwordConfirmation = document.getElementById('password_confirmation');

            form.addEventListener('submit', function(e) {
                // Check if password_confirmation is filled but password is not
                if (passwordConfirmation.value && !password.value) {
                    e.preventDefault();
                    alert('Je moet een nieuw wachtwoord invoeren om het te bevestigen.');
                    password.focus();
                    return false;
                }

                // Check if password is filled but current_password is not
                if (password.value && !currentPassword.value) {
                    e.preventDefault();
                    alert('Je moet je huidige wachtwoord invoeren om een nieuw wachtwoord in te stellen.');
                    currentPassword.focus();
                    return false;
                }

                // Check if passwords match when both are filled
                if (password.value && passwordConfirmation.value && password.value !== passwordConfirmation.value) {
                    e.preventDefault();
                    alert('De wachtwoorden komen niet overeen.');
                    passwordConfirmation.focus();
                    return false;
                }
            });

            // Real-time validation feedback
            passwordConfirmation.addEventListener('input', function() {
                if (this.value && !password.value) {
                    this.setCustomValidity('Vul eerst een nieuw wachtwoord in');
                } else {
                    this.setCustomValidity('');
                }
            });

            password.addEventListener('input', function() {
                if (this.value && !currentPassword.value) {
                    this.setCustomValidity('Vul eerst je huidige wachtwoord in');
                } else {
                    this.setCustomValidity('');
                }
                // Clear password_confirmation validity when password changes
                passwordConfirmation.setCustomValidity('');
            });

            currentPassword.addEventListener('input', function() {
                // Clear password validity when current password is filled
                password.setCustomValidity('');
            });
        });
    </script>
@endsection

