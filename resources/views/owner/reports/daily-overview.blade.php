@extends('layouts.app-neu')

@php
    $portalName = 'Owner';
@endphp

@section('page-title', 'Dagoverzicht Rapportage')

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
    <a href="{{ route('owner.issues.index') }}">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
        Issues
    </a>
    <a href="{{ route('owner.reports.daily') }}" class="active">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
        </svg>
        Rapporten
    </a>
@endsection

@section('content')
    <!-- Date Selector & CSV Export -->
    <div class="neu-widget" style="margin-bottom: 1.5rem;">
        <div class="widget-body">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <!-- Date Picker -->
                <div>
                    <form action="{{ route('owner.reports.daily') }}" method="GET">
                        <label for="date" class="neu-label">Selecteer Datum</label>
                        <div style="display: flex; gap: 0.75rem;">
                            <input
                                type="date"
                                id="date"
                                name="date"
                                class="neu-input"
                                value="{{ $date->format('Y-m-d') }}"
                                style="flex: 1; margin: 0;"
                            >
                            <button type="submit" class="neu-button-primary">Toon</button>
                        </div>
                    </form>
                </div>

                <!-- CSV Export -->
                <div>
                    <form action="{{ route('owner.reports.export-csv') }}" method="POST">
                        @csrf
                        <label class="neu-label">Exporteer naar CSV</label>
                        <div style="display: flex; gap: 0.5rem; align-items: flex-end;">
                            <div class="flex-1">
                                <input
                                    type="date"
                                    name="start_date"
                                    class="neu-input"
                                    value="{{ today()->subDays(7)->format('Y-m-d') }}"
                                    style="margin: 0; font-size: 0.875rem;"
                                    required
                                >
                            </div>
                            <span style="padding: 0.5rem;">tot</span>
                            <div class="flex-1">
                                <input
                                    type="date"
                                    name="end_date"
                                    class="neu-input"
                                    value="{{ today()->format('Y-m-d') }}"
                                    style="margin: 0; font-size: 0.875rem;"
                                    required
                                >
                            </div>
                            <button type="submit" class="neu-button-secondary">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" style="display: inline-block; vertical-align: middle;">
                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                                CSV
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        <div class="neu-widget">
            <div class="widget-body" class="text-center">
                <div class="summary-label">Totaal Taken</div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--primary);">{{ $stats['total'] }}</div>
            </div>
        </div>

        <div class="neu-widget">
            <div class="widget-body" class="text-center">
                <div class="summary-label">Voltooid</div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--success);">{{ $stats['completed'] }}</div>
            </div>
        </div>

        <div class="neu-widget">
            <div class="widget-body" class="text-center">
                <div class="summary-label">Bezig</div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--warning);">{{ $stats['in_progress'] }}</div>
            </div>
        </div>

        <div class="neu-widget">
            <div class="widget-body" class="text-center">
                <div class="summary-label">Wachtend</div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--text-muted);">{{ $stats['pending'] }}</div>
            </div>
        </div>
    </div>

    <!-- Additional Stats -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        <div class="neu-widget">
            <div class="widget-body">
                <div class="summary-label">Totale Geplande Tijd</div>
                <div style="font-size: 1.5rem; font-weight: 600;">{{ $stats['total_planned_duration'] }} min</div>
            </div>
        </div>

        <div class="neu-widget">
            <div class="widget-body">
                <div class="summary-label">Totale Werkelijke Tijd</div>
                <div style="font-size: 1.5rem; font-weight: 600;">{{ $stats['total_actual_duration'] ?? 0 }} min</div>
            </div>
        </div>

        <div class="neu-widget">
            <div class="widget-body">
                <div class="summary-label">Gemiddelde Werkelijke Duur</div>
                <div style="font-size: 1.5rem; font-weight: 600;">
                    {{ $stats['average_actual_duration'] ? round($stats['average_actual_duration']) : 0 }} min
                </div>
            </div>
        </div>
    </div>

    <!-- Tasks Table -->
    <div class="neu-widget">
        <div class="widget-header">
            <h2>Taken voor {{ $date->format('d-m-Y') }}</h2>
        </div>
        <div class="widget-body">
            @if($tasks->count() > 0)
                <div class="neu-table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Kamer</th>
                                <th>Schoonmaker</th>
                                <th>Check-in</th>
                                <th>Suggestie Start</th>
                                <th>Gepland</th>
                                <th>Werkelijk</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tasks as $task)
                            <tr>
                                <td class="fw-600">{{ $task->room->room_number }}</td>
                                <td>{{ $task->cleaner->user->name }}</td>
                                <td>{{ $task->booking ? $task->booking->check_in_datetime->format('H:i') : '-' }}</td>
                                <td>{{ $task->suggested_start_time ? $task->suggested_start_time->format('H:i') : '-' }}</td>
                                <td>{{ $task->planned_duration }} min</td>
                                <td>
                                    @if($task->actual_duration)
                                        <span class="fw-600">{{ $task->actual_duration }} min</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($task->status === 'completed')
                                        <span class="neu-badge success">Voltooid</span>
                                    @elseif($task->status === 'in_progress')
                                        <span class="neu-badge warning">Bezig</span>
                                    @else
                                        <span class="neu-badge">Wachtend</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="neu-empty-state">
                    <div class="empty-icon">
                        <svg width="48" height="48" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="empty-title">Geen taken voor deze datum</div>
                    <div class="empty-message">Er zijn geen schoonmaaktaken gepland voor {{ $date->format('d-m-Y') }}</div>
                </div>
            @endif
        </div>
    </div>
@endsection
