@extends('layouts.app-neu')

@section('title', 'Dagelijks Overzicht')

@php
    $portalName = 'Owner Portal';
@endphp

@section('page-title', 'Dagelijks Overzicht - ' . $date->format('d-m-Y'))

@section('nav')
    <a href="{{ route('owner.dashboard') }}" class="nav-section-link">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
        </svg>
        Dashboard
    </a>
    <a href="{{ route('owner.reports.daily') }}" class="nav-section-link active">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
            <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
        </svg>
        Dagelijks Overzicht
    </a>
@endsection

@section('content')

<!-- Date Selector -->
<div class="neu-card" style="margin-bottom: 24px;">
    <form method="GET" action="{{ route('owner.reports.daily') }}" style="display: flex; gap: 16px; align-items: flex-end;">
        <div class="form-group" style="flex: 1; max-width: 300px; margin: 0;">
            <label for="date" class="form-label">Selecteer Datum</label>
            <input type="date" id="date" name="date" class="form-input" value="{{ $date->format('Y-m-d') }}">
        </div>
        <button type="submit" class="neu-button-primary">
            <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
            </svg>
            Toon
        </button>
    </form>
</div>

<!-- Statistics Cards -->
<div class="neu-dashboard-grid" style="margin-bottom: 24px;">
    <!-- Total Tasks -->
    <div class="neu-stat-card">
        <div class="stat-icon">
            <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div class="stat-label">Totaal Taken</div>
        <div class="stat-value">{{ $stats['total'] }}</div>
    </div>

    <!-- Completed -->
    <div class="neu-stat-card">
        <div class="stat-icon" style="color: #10b981;">
            <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div class="stat-label">Voltooid</div>
        <div class="stat-value" style="color: #10b981;">{{ $stats['completed'] }}</div>
    </div>

    <!-- In Progress -->
    <div class="neu-stat-card">
        <div class="stat-icon" style="color: #3b82f6;">
            <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div class="stat-label">Bezig</div>
        <div class="stat-value" style="color: #3b82f6;">{{ $stats['in_progress'] }}</div>
    </div>

    <!-- Pending -->
    <div class="neu-stat-card">
        <div class="stat-icon" style="color: #f59e0b;">
            <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div class="stat-label">Te Doen</div>
        <div class="stat-value" style="color: #f59e0b;">{{ $stats['pending'] }}</div>
    </div>
</div>

<!-- Duration Statistics -->
@if($stats['total'] > 0)
<div class="neu-card" style="margin-bottom: 24px;">
    <h3 class="card-title">Tijdstatistieken</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-top: 16px;">
        <div style="padding: 16px; background: var(--neu-bg-light); border-radius: 12px;">
            <div style="font-size: 14px; color: var(--neu-text-secondary); margin-bottom: 8px;">Totaal Gepland</div>
            <div style="font-size: 24px; font-weight: 600; color: var(--neu-text-primary);">
                {{ number_format($stats['total_planned_duration'], 0) }} min
            </div>
        </div>

        @if($stats['total_actual_duration'] > 0)
        <div style="padding: 16px; background: var(--neu-bg-light); border-radius: 12px;">
            <div style="font-size: 14px; color: var(--neu-text-secondary); margin-bottom: 8px;">Totaal Werkelijk</div>
            <div style="font-size: 24px; font-weight: 600; color: var(--neu-text-primary);">
                {{ number_format($stats['total_actual_duration'], 0) }} min
            </div>
        </div>

        <div style="padding: 16px; background: var(--neu-bg-light); border-radius: 12px;">
            <div style="font-size: 14px; color: var(--neu-text-secondary); margin-bottom: 8px;">Gemiddeld Werkelijk</div>
            <div style="font-size: 24px; font-weight: 600; color: var(--neu-text-primary);">
                {{ number_format($stats['average_actual_duration'], 0) }} min
            </div>
        </div>

        <div style="padding: 16px; background: var(--neu-bg-light); border-radius: 12px;">
            <div style="font-size: 14px; color: var(--neu-text-secondary); margin-bottom: 8px;">Verschil</div>
            @php
                $difference = $stats['total_actual_duration'] - $stats['total_planned_duration'];
                $color = $difference > 0 ? '#ef4444' : '#10b981';
            @endphp
            <div style="font-size: 24px; font-weight: 600; color: {{ $color }};">
                {{ $difference > 0 ? '+' : '' }}{{ number_format($difference, 0) }} min
            </div>
        </div>
        @endif
    </div>
</div>
@endif

<!-- Tasks Table -->
<div class="neu-card">
    <div class="card-header-actions">
        <h3 class="card-title">Schoonmaaktaken voor {{ $date->format('d-m-Y') }}</h3>

        <!-- Export Button -->
        <form action="{{ route('owner.reports.export-csv') }}" method="POST" style="display: inline;">
            @csrf
            <input type="hidden" name="start_date" value="{{ $date->format('Y-m-d') }}">
            <input type="hidden" name="end_date" value="{{ $date->format('Y-m-d') }}">
            <button type="submit" class="neu-button-secondary">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
                Export CSV
            </button>
        </form>
    </div>

    <div class="table-container">
        <table class="neu-table">
            <thead>
                <tr>
                    <th>Kamer</th>
                    <th>Schoonmaker</th>
                    <th>Check-in Tijd</th>
                    <th>Suggestie Start</th>
                    <th>Gepland (min)</th>
                    <th>Werkelijk (min)</th>
                    <th>Status</th>
                    <th>Notities</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tasks as $task)
                    <tr>
                        <td><strong>{{ $task->room->room_number }}</strong></td>
                        <td>
                            @if($task->cleaner)
                                {{ $task->cleaner->user->name }}
                            @else
                                <span class="text-muted">Niet toegewezen</span>
                            @endif
                        </td>
                        <td>
                            @if($task->booking)
                                {{ $task->booking->check_in_datetime->format('H:i') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($task->suggested_start_time)
                                {{ $task->suggested_start_time->format('H:i') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $task->planned_duration }}</td>
                        <td>
                            @if($task->actual_duration)
                                <span style="color: {{ $task->actual_duration > $task->planned_duration ? '#ef4444' : '#10b981' }};">
                                    {{ $task->actual_duration }}
                                </span>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-{{ $task->status === 'completed' ? 'success' : ($task->status === 'in_progress' ? 'info' : 'warning') }}">
                                @if($task->status === 'completed')
                                    Voltooid
                                @elseif($task->status === 'in_progress')
                                    Bezig
                                @else
                                    Te Doen
                                @endif
                            </span>
                        </td>
                        <td>
                            @if($task->booking && $task->booking->notes)
                                <span title="{{ $task->booking->notes }}">{{ Str::limit($task->booking->notes, 30) }}</span>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Geen taken gevonden voor deze datum</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
