@extends('layouts.app-neu')

@php
    $portalName = 'Owner';
@endphp

@section('page-title', $cleaner->user->name)

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
    <a href="{{ route('owner.cleaners.index') }}" class="active">
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
            <h2>Schoonmaker Details</h2>
            <a href="{{ route('owner.cleaners.index') }}" class="neu-button-secondary">Terug</a>
        </div>
        <div class="widget-body">
            <div class="info-grid">
                <div>
                    <div class="info-label">Naam</div>
                    <div class="info-value room-info-large">{{ $cleaner->user->name }}</div>
                </div>
                <div>
                    <div class="info-label">Email</div>
                    <div class="info-value">{{ $cleaner->user->email }}</div>
                </div>
                <div>
                    <div class="info-label">Status</div>
                    <div>
                        @if($cleaner->status === 'active')
                            <span class="neu-badge success">Actief</span>
                        @else
                            <span class="neu-badge">Gedeactiveerd</span>
                        @endif
                    </div>
                </div>
                <div>
                    <div class="info-label">Toegevoegd</div>
                    <div class="info-value">{{ $cleaner->created_at->format('d-m-Y') }}</div>
                </div>
            </div>
        </div>
    </div>

    @if($cleaner->cleaningTasks->count() > 0)
    <div class="neu-widget" class="mt-6">
        <div class="widget-header">
            <h2>Recente Taken (Laatste 7 Dagen)</h2>
        </div>
        <div class="widget-body">
            <div class="neu-table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Datum</th>
                            <th>Kamer</th>
                            <th>Check-in Tijd</th>
                            <th>Status</th>
                            <th>Werkelijke Duur</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cleaner->cleaningTasks as $task)
                        <tr>
                            <td>{{ $task->date->format('d-m-Y') }}</td>
                            <td class="info-value">{{ $task->room->room_number }}</td>
                            <td>{{ $task->booking ? $task->booking->check_in_datetime->format('H:i') : '-' }}</td>
                            <td>
                                @if($task->status === 'completed')
                                    <span class="neu-badge success">Voltooid</span>
                                @elseif($task->status === 'in_progress')
                                    <span class="neu-badge warning">Bezig</span>
                                @else
                                    <span class="neu-badge">Pending</span>
                                @endif
                            </td>
                            <td>
                                @if($task->actual_duration)
                                    {{ $task->actual_duration }} min
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @else
        <div class="neu-widget" class="mt-6">
            <div class="widget-body">
                <div class="neu-empty-state">
                    <div class="empty-icon">
                        <svg width="48" height="48" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="empty-title">Geen recente taken</div>
                    <div class="empty-message">Deze schoonmaker heeft de afgelopen 7 dagen geen taken uitgevoerd</div>
                </div>
            </div>
        </div>
    @endif
@endsection
