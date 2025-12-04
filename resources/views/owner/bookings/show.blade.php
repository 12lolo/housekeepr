@extends('layouts.app-neu')

@php
    $portalName = 'Owner';
@endphp

@section('page-title', 'Boeking Details')

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
            <h2>Boeking Details</h2>
            <div class="d-flex gap-3">
                <a href="{{ route('owner.bookings.edit', $booking) }}" class="neu-button-primary">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" class="icon-inline">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                    </svg>
                    Bewerken
                </a>
                <a href="{{ route('owner.bookings.index') }}" class="neu-button-secondary">Terug</a>
            </div>
        </div>
        <div class="widget-body">
            <div class="info-grid mb-6">
                <div>
                    <div class="info-label">Kamer</div>
                    <div class="info-value room-info-large">{{ $booking->room->room_number }}</div>
                    @if($booking->room->room_type)
                        <div class="room-type-hint">{{ $booking->room->room_type }}</div>
                    @endif
                </div>
                <div>
                    <div class="info-label">Check-in</div>
                    <div class="info-value">{{ $booking->check_in_datetime->format('d-m-Y H:i') }}</div>
                </div>
                <div>
                    <div class="info-label">Aangemaakt</div>
                    <div class="info-value">{{ $booking->created_at->format('d-m-Y H:i') }}</div>
                </div>
            </div>

            @if($booking->notes)
            <div class="mt-6">
                <div class="notes-label">Notities</div>
                <div class="notes-content">
                    {{ $booking->notes }}
                </div>
            </div>
            @endif
        </div>
    </div>

    @if($booking->cleaningTask)
    <div class="neu-widget" class="mt-6">
        <div class="widget-header">
            <h2>Schoonmaaktaak</h2>
        </div>
        <div class="widget-body">
            <div class="info-grid mb-6">
                <div>
                    <div class="info-label">Schoonmaker</div>
                    <div class="info-value">{{ $booking->cleaningTask->cleaner->user->name }}</div>
                </div>
                <div>
                    <div class="info-label">Suggestie Starttijd</div>
                    <div class="info-value">{{ $booking->cleaningTask->suggested_start_time ? $booking->cleaningTask->suggested_start_time->format('d-m-Y H:i') : '-' }}</div>
                </div>
                <div>
                    <div class="info-label">Geplande Duur</div>
                    <div class="info-value">{{ $booking->cleaningTask->planned_duration }} min</div>
                </div>
                <div>
                    <div class="info-label">Status</div>
                    <div>
                        @if($booking->cleaningTask->status === 'completed')
                            <span class="neu-badge success">Voltooid</span>
                        @elseif($booking->cleaningTask->status === 'in_progress')
                            <span class="neu-badge warning">Bezig</span>
                        @else
                            <span class="neu-badge">Pending</span>
                        @endif
                    </div>
                </div>
            </div>

            @if($booking->cleaningTask->actual_start_time)
            <div class="info-grid">
                <div>
                    <div class="info-label">Werkelijke Start</div>
                    <div class="info-value">{{ $booking->cleaningTask->actual_start_time->format('d-m-Y H:i') }}</div>
                </div>
                @if($booking->cleaningTask->actual_end_time)
                <div>
                    <div class="info-label">Werkelijke Eind</div>
                    <div class="info-value">{{ $booking->cleaningTask->actual_end_time->format('d-m-Y H:i') }}</div>
                </div>
                <div>
                    <div class="info-label">Werkelijke Duur</div>
                    <div class="info-value">{{ $booking->cleaningTask->actual_duration }} min</div>
                </div>
                @endif
            </div>
            @endif

            @if($booking->cleaningTask->taskLogs && $booking->cleaningTask->taskLogs->count() > 0)
            <div class="mt-6">
                <div class="notes-label">Taak Logs</div>
                <div class="neu-table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Tijd</th>
                                <th>Actie</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($booking->cleaningTask->taskLogs as $log)
                            <tr>
                                <td>{{ $log->timestamp->format('d-m-Y H:i:s') }}</td>
                                <td>
                                    @if($log->action === 'started')
                                        <span class="neu-badge warning">Gestart</span>
                                    @elseif($log->action === 'stopped')
                                        <span class="neu-badge">Gepauzeerd</span>
                                    @elseif($log->action === 'completed')
                                        <span class="neu-badge success">Voltooid</span>
                                    @else
                                        {{ ucfirst($log->action) }}
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
    @else
        <div class="neu-widget" class="mt-6">
            <div class="widget-body">
                <div class="neu-empty-state">
                    <div class="empty-icon">
                        <svg width="48" height="48" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="empty-title">Geen schoonmaaktaak</div>
                    <div class="empty-message">Er is nog geen schoonmaaktaak aangemaakt voor deze boeking</div>
                </div>
            </div>
        </div>
    @endif
@endsection
