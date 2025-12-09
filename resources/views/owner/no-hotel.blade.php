@extends('layouts.app-neu')

@php
    $portalName = 'Owner';
@endphp

@section('page-title', 'Welkom bij HouseKeepr')

@section('nav')
    <a href="{{ route('owner.dashboard') }}" class="active">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
        </svg>
        Dashboard
    </a>
@endsection

@section('content')
    <div class="neu-widget" style="max-width: 800px; margin: 0 auto;">
        <div class="widget-header" style="text-align: center; flex-direction: column; gap: 1rem;">
            <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                <svg width="40" height="40" fill="white" viewBox="0 0 20 20">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                </svg>
            </div>
            <h2 style="margin: 0;">Welkom bij HouseKeepr!</h2>
            <p style="margin: 0; color: #6b7280; font-size: 1rem;">Je bent bijna klaar om te beginnen</p>
        </div>

        <div class="widget-body" style="padding: 2rem;">
            <!-- Welcome Message -->
            <div style="background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 1px solid #667eea30;">
                <h3 style="margin: 0 0 0.5rem 0; color: #667eea; font-size: 1.1rem;">👋 Welkom, {{ auth()->user()->name }}!</h3>
                <p style="margin: 0; color: #4b5563; line-height: 1.6;">
                    Om HouseKeepr te kunnen gebruiken, moet je eerst je hotel aanmaken.
                    Zodra je hotel is aangemaakt, kun je kamers toevoegen, schoonmakers beheren, en boekingen plannen.
                </p>
            </div>

            <!-- Setup Steps -->
            <div style="margin-bottom: 2rem;">
                <h3 style="margin: 0 0 1rem 0; font-size: 1rem; color: #374151;">Wat gebeurt er na het aanmaken van je hotel?</h3>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div style="display: flex; gap: 1rem; align-items: start;">
                        <div style="width: 32px; height: 32px; background: #667eea; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-weight: 600;">1</div>
                        <div>
                            <strong style="color: #374151;">Hotel aanmaken</strong>
                            <p style="margin: 0.25rem 0 0 0; color: #6b7280; font-size: 0.9rem;">Vul je hotelnaam en adres in</p>
                        </div>
                    </div>
                    <div style="display: flex; gap: 1rem; align-items: start;">
                        <div style="width: 32px; height: 32px; background: #667eea; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-weight: 600;">2</div>
                        <div>
                            <strong style="color: #374151;">Kamers toevoegen</strong>
                            <p style="margin: 0.25rem 0 0 0; color: #6b7280; font-size: 0.9rem;">Voeg je hotelkamers toe met types en prijzen</p>
                        </div>
                    </div>
                    <div style="display: flex; gap: 1rem; align-items: start;">
                        <div style="width: 32px; height: 32px; background: #667eea; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-weight: 600;">3</div>
                        <div>
                            <strong style="color: #374151;">Schoonmakers koppelen</strong>
                            <p style="margin: 0.25rem 0 0 0; color: #6b7280; font-size: 0.9rem;">Voeg je schoonmaakpersoneel toe</p>
                        </div>
                    </div>
                    <div style="display: flex; gap: 1rem; align-items: start;">
                        <div style="width: 32px; height: 32px; background: #667eea; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-weight: 600;">4</div>
                        <div>
                            <strong style="color: #374151;">Start met plannen</strong>
                            <p style="margin: 0.25rem 0 0 0; color: #6b7280; font-size: 0.9rem;">Begin met boekingen en schoonmaakplanning</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Steps -->
            <div style="display: flex; justify-content: center; align-items: center; margin-bottom: 2rem; gap: 0.5rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <div style="width: 32px; height: 32px; background: #10b981; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px;">✓</div>
                    <span style="color: #10b981; font-weight: 600;">Account</span>
                </div>
                <svg width="20" height="20" fill="#d1d5db" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <div style="width: 32px; height: 32px; background: #667eea; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px;">2</div>
                    <span style="font-weight: 600; color: #667eea;">Hotel</span>
                </div>
            </div>

            <!-- Create Hotel Form -->
            <div style="background: #f9fafb; padding: 1.5rem; border-radius: 12px; border: 2px dashed #d1d5db;">
                <h3 style="margin: 0 0 1.5rem 0; font-size: 1.1rem; color: #374151;">Maak je hotel aan</h3>

                <form action="{{ route('owner.hotels.store') }}" method="POST" id="createHotelForm">
                    @csrf

                    <div class="neu-form-group">
                        <label for="hotel_name" class="neu-label">Hotel Naam <span style="color: #e53e3e;">*</span></label>
                        <input
                            type="text"
                            id="hotel_name"
                            name="name"
                            class="neu-input"
                            placeholder="Bijv. Hotel De Gouden Leeuw"
                            value="{{ old('name') }}"
                            required
                            autofocus
                        >
                        @error('name')
                            <span class="neu-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div style="margin: 1.5rem 0;">
                        <h4 style="margin: 0 0 1rem 0; font-size: 0.95rem; color: #374151; font-weight: 600;">Adresgegevens</h4>

                        <div class="neu-form-group">
                            <label for="street" class="neu-label">Straat <span style="color: #e53e3e;">*</span></label>
                            <input
                                type="text"
                                id="street"
                                name="street"
                                class="neu-input"
                                placeholder="Hoofdstraat"
                                value="{{ old('street') }}"
                                required
                            >
                            @error('street')
                                <span class="neu-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem;">
                            <div class="neu-form-group">
                                <label for="house_number" class="neu-label">Huisnummer <span style="color: #e53e3e;">*</span></label>
                                <input
                                    type="text"
                                    id="house_number"
                                    name="house_number"
                                    class="neu-input"
                                    placeholder="123"
                                    value="{{ old('house_number') }}"
                                    required
                                >
                                @error('house_number')
                                    <span class="neu-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="neu-form-group">
                                <label for="house_number_addition" class="neu-label">Toevoeging</label>
                                <input
                                    type="text"
                                    id="house_number_addition"
                                    name="house_number_addition"
                                    class="neu-input"
                                    placeholder="A"
                                    value="{{ old('house_number_addition') }}"
                                >
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1rem;">
                            <div class="neu-form-group">
                                <label for="postal_code" class="neu-label">Postcode <span style="color: #e53e3e;">*</span></label>
                                <input
                                    type="text"
                                    id="postal_code"
                                    name="postal_code"
                                    class="neu-input"
                                    placeholder="1234 AB"
                                    value="{{ old('postal_code') }}"
                                    required
                                >
                                @error('postal_code')
                                    <span class="neu-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="neu-form-group">
                                <label for="city" class="neu-label">Plaats <span style="color: #e53e3e;">*</span></label>
                                <input
                                    type="text"
                                    id="city"
                                    name="city"
                                    class="neu-input"
                                    placeholder="Amsterdam"
                                    value="{{ old('city') }}"
                                    required
                                >
                                @error('city')
                                    <span class="neu-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="neu-form-group">
                            <label for="country" class="neu-label">Land <span style="color: #e53e3e;">*</span></label>
                            <select id="country" name="country" class="neu-input" required>
                                <option value="Nederland" {{ old('country') == 'Nederland' ? 'selected' : '' }}>Nederland</option>
                                <option value="België" {{ old('country') == 'België' ? 'selected' : '' }}>België</option>
                                <option value="Duitsland" {{ old('country') == 'Duitsland' ? 'selected' : '' }}>Duitsland</option>
                                <option value="Frankrijk" {{ old('country') == 'Frankrijk' ? 'selected' : '' }}>Frankrijk</option>
                                <option value="Luxemburg" {{ old('country') == 'Luxemburg' ? 'selected' : '' }}>Luxemburg</option>
                                <option value="Anders">Anders</option>
                            </select>
                            @error('country')
                                <span class="neu-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                        <button type="submit" class="neu-button-primary" style="padding: 0.75rem 2rem; font-size: 1rem;">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20" class="icon-inline">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                            </svg>
                            Hotel Aanmaken
                        </button>
                    </div>
                </form>
            </div>

            <!-- Help Text -->
            <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e5e7eb; text-align: center;">
                <p style="margin: 0; color: #6b7280; font-size: 0.9rem;">
                    Hulp nodig? Neem contact op met de beheerder.
                </p>
            </div>
        </div>
    </div>
@endsection

