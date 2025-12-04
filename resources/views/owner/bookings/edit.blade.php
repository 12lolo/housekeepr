@extends('layouts.app-neu')

@php
    $portalName = 'Owner';
@endphp

@section('page-title', 'Boeking Bewerken')

@section('nav')
    <a href="{{ route('owner.dashboard') }}">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
        </svg>
        Dashboard
    </a>
    <a href="{{ route('owner.rooms.index') }}">
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
    <a href="{{ route('owner.bookings.index') }}" class="active">
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
            <h2>Boeking Bewerken</h2>
            <a href="{{ route('owner.bookings.index') }}" class="neu-button-secondary">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" class="icon-inline">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                </svg>
                Terug
            </a>
        </div>
        <div class="widget-body">
            <form action="{{ route('owner.bookings.update', $booking) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="neu-form-group">
                    <label for="room_id" class="neu-label">Kamer</label>
                    <select
                        id="room_id"
                        name="room_id"
                        class="neu-input @error('room_id') error @enderror"
                        required
                    >
                        <option value="">Selecteer een kamer</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}" {{ old('room_id', $booking->room_id) == $room->id ? 'selected' : '' }}>
                                {{ $room->room_number }} @if($room->room_type) - {{ $room->room_type }} @endif
                            </option>
                        @endforeach
                    </select>
                    @error('room_id')
                        <span class="neu-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="neu-form-group">
                    <label for="check_in_datetime" class="neu-label">Check-in Datum & Tijd</label>
                    <input
                        type="datetime-local"
                        id="check_in_datetime"
                        name="check_in_datetime"
                        class="neu-input @error('check_in_datetime') error @enderror"
                        value="{{ old('check_in_datetime', $booking->check_in_datetime->format('Y-m-d\TH:i')) }}"
                        required
                    >
                    @error('check_in_datetime')
                        <span class="neu-error">{{ $message }}</span>
                    @enderror
                    <small class="neu-hint">Als je de tijd wijzigt, wordt de schoonmaaktaak opnieuw ingepland.</small>
                </div>

                <div class="neu-form-group">
                    <label for="notes" class="neu-label">Notities (optioneel)</label>
                    <textarea
                        id="notes"
                        name="notes"
                        class="neu-input @error('notes') error @enderror"
                        rows="4"
                        placeholder="Bijv. speciale verzoeken, opmerkingen..."
                    >{{ old('notes', $booking->notes) }}</textarea>
                    @error('notes')
                        <span class="neu-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-actions">
                    <button type="submit" class="neu-button-primary">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" class="icon-inline">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Wijzigingen Opslaan
                    </button>
                    <a href="{{ route('owner.bookings.index') }}" class="neu-button-secondary">Annuleren</a>
                </div>
            </form>
        </div>
    </div>
@endsection
