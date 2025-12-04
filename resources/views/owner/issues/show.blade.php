@extends('layouts.app-neu')

@php
    $portalName = 'Owner';
@endphp

@section('page-title', 'Issue Details')

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
    <a href="{{ route('owner.issues.index') }}" class="active">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
        Issues
    </a>
@endsection

@section('content')
    <div class="neu-widget">
        <div class="widget-header">
            <h2>Issue Details</h2>
            <div class="d-flex gap-3">
                @if($issue->status === 'open')
                    <form action="{{ route('owner.issues.mark-fixed', $issue) }}" method="POST">
                        @csrf
                        <button type="submit" class="neu-button-primary">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" class="icon-inline">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Markeer als Gefixt
                        </button>
                    </form>
                @else
                    <form action="{{ route('owner.issues.reopen', $issue) }}" method="POST">
                        @csrf
                        <button type="submit" class="neu-button-primary">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" class="icon-inline">
                                <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
                            </svg>
                            Heropen Issue
                        </button>
                    </form>
                @endif
                <a href="{{ route('owner.issues.index') }}" class="neu-button-secondary">Terug</a>
            </div>
        </div>
        <div class="widget-body">
            <div class="info-grid mb-6">
                <div>
                    <div class="info-label">Kamer</div>
                    <div class="info-value room-info-large">{{ $issue->room->room_number }}</div>
                    @if($issue->room->room_type)
                        <div class="room-type-hint">{{ $issue->room->room_type }}</div>
                    @endif
                </div>
                <div>
                    <div class="info-label">Impact</div>
                    <div>
                        @if($issue->impact === 'kan_niet_gebruikt')
                            <span class="neu-badge danger">Kamer Geblokkeerd</span>
                        @elseif($issue->impact === 'graag_snel')
                            <span class="neu-badge warning">Graag Snel</span>
                        @else
                            <span class="neu-badge">Geen Haast</span>
                        @endif
                    </div>
                </div>
                <div>
                    <div class="info-label">Status</div>
                    <div>
                        @if($issue->status === 'open')
                            <span class="neu-badge danger">Open</span>
                        @else
                            <span class="neu-badge success">Gefixt</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="info-grid mb-6">
                <div>
                    <div class="info-label">Gerapporteerd Door</div>
                    <div class="info-value">{{ $issue->reportedBy->name }}</div>
                </div>
                <div>
                    <div class="info-label">Gemeld Op</div>
                    <div class="info-value">{{ $issue->created_at->format('d-m-Y H:i') }}</div>
                </div>
            </div>

            <div class="mt-6">
                <div class="notes-label">Beschrijving</div>
                <div class="issue-description">{{ $issue->note }}</div>
            </div>

            @if($issue->photo_path)
            <div class="mt-6">
                <div class="notes-label">Foto</div>
                <div class="notes-content">
                    <img src="{{ asset('storage/' . $issue->photo_path) }}" alt="Issue Photo" class="issue-photo">
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection
