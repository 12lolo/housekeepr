@extends('layouts.app-neu')

@php
    $portalName = 'Owner';
@endphp

@section('page-title', 'Nieuwe Kamer')

@section('nav')
    <a href="{{ route('owner.dashboard') }}">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
        </svg>
        Dashboard
    </a>
    <a href="{{ route('owner.rooms.index') }}" class="active">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
            <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Kamers
    </a>
    <a href="{{ route('owner.cleaners.index') }}">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
        </svg>
        Schoonmakers
    </a>
    <a href="{{ route('owner.bookings.index') }}">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
        </svg>
        Boekingen
    </a>
    <a href="{{ route('owner.capacity.index') }}">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
            <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
        </svg>
        Capaciteit
    </a>
    <a href="{{ route('owner.issues.index') }}">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
        Issues
    </a>
@endsection

@section('content')
    <div class="neu-widget">
        <div class="widget-header">
            <h2>Nieuwe Kamer Aanmaken</h2>
            <a href="{{ route('owner.rooms.index') }}" class="neu-button-secondary">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" class="icon-inline">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                </svg>
                Terug
            </a>
        </div>
        <div class="widget-body">
            <form action="{{ route('owner.rooms.store') }}" method="POST">
                @csrf

                <div class="neu-form-group">
                    <label for="room_number" class="neu-label">Kamernummer</label>
                    <input
                        type="text"
                        id="room_number"
                        name="room_number"
                        class="neu-input @error('room_number') error @enderror"
                        value="{{ old('room_number') }}"
                        placeholder="Bijv. 101"
                        required
                    >
                    @error('room_number')
                        <span class="neu-error">{{ $message }}</span>
                    @enderror
                    <small class="neu-hint">Moet uniek zijn binnen dit hotel.</small>
                </div>

                <div class="neu-form-group">
                    <label for="room_type" class="neu-label">Kamertype (optioneel)</label>
                    <input
                        type="text"
                        id="room_type"
                        name="room_type"
                        class="neu-input @error('room_type') error @enderror"
                        value="{{ old('room_type') }}"
                        placeholder="Bijv. Deluxe, Standard, Suite"
                    >
                    @error('room_type')
                        <span class="neu-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="neu-form-group">
                    <label for="standard_duration" class="neu-label">Standaard Schoontijd (minuten)</label>
                    <input
                        type="number"
                        id="standard_duration"
                        name="standard_duration"
                        class="neu-input @error('standard_duration') error @enderror"
                        value="{{ old('standard_duration', 30) }}"
                        min="1"
                        max="480"
                        required
                    >
                    @error('standard_duration')
                        <span class="neu-error">{{ $message }}</span>
                    @enderror
                    <small class="neu-hint">Hoeveel minuten het normaal kost om deze kamer schoon te maken (max 480 minuten = 8 uur).</small>
                </div>

                <div class="form-actions">
                    <button type="submit" class="neu-button-primary">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" class="icon-inline">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Kamer Aanmaken
                    </button>
                    <a href="{{ route('owner.rooms.index') }}" class="neu-button-secondary">Annuleren</a>
                </div>
            </form>
        </div>
    </div>
@endsection
