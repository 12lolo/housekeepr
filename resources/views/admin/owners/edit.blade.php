@extends('layouts.app-neu')

@php
    $portalName = 'Admin';
@endphp

@section('page-title', 'Eigenaar Bewerken')

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
            <h2>{{ $owner->name }} Bewerken</h2>
            <a href="{{ route('admin.owners.index') }}" class="neu-button-secondary">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" class="icon-inline">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                </svg>
                Terug
            </a>
        </div>
        <div class="widget-body">
            <form action="{{ route('admin.owners.update', $owner) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="neu-form-group">
                    <label for="name" class="neu-label">Naam Eigenaar</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="neu-input @error('name') error @enderror"
                        value="{{ old('name', $owner->name) }}"
                        required
                    >
                    @error('name')
                        <span class="neu-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="neu-form-group">
                    <label for="email" class="neu-label">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="neu-input @error('email') error @enderror"
                        value="{{ old('email', $owner->email) }}"
                        required
                    >
                    @error('email')
                        <span class="neu-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="neu-form-group">
                    <label for="hotel_name" class="neu-label">Hotelnaam</label>
                    <input
                        type="text"
                        id="hotel_name"
                        name="hotel_name"
                        class="neu-input @error('hotel_name') error @enderror"
                        value="{{ old('hotel_name', $owner->hotel->name ?? '') }}"
                        required
                    >
                    @error('hotel_name')
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
                    <a href="{{ route('admin.owners.index') }}" class="neu-button-secondary">Annuleren</a>
                </div>
            </form>
        </div>
    </div>
@endsection
