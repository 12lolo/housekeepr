@extends('layouts.app-neu')

@section('title', 'Schoonmaker Prestaties')

@php
    $portalName = 'Owner Portal';
@endphp

@section('page-title', 'Schoonmaker Prestaties')

@section('nav')
    <a href="{{ route('owner.dashboard') }}" class="nav-section-link">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
        </svg>
        Dashboard
    </a>
    <a href="{{ route('owner.reports.cleaner-performance') }}" class="nav-section-link active">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
        </svg>
        Prestaties
    </a>
@endsection

@section('content')

<!-- Date Range Selector -->
<div class="neu-card" style="margin-bottom: 24px;">
    <form method="GET" action="{{ route('owner.reports.cleaner-performance') }}" style="display: flex; gap: 16px; align-items: flex-end; flex-wrap: wrap;">
        <div class="form-group" style="flex: 1; min-width: 200px; margin: 0;">
            <label for="start_date" class="form-label">Van Datum</label>
            <input type="date" id="start_date" name="start_date" class="form-input" value="{{ $startDate->format('Y-m-d') }}">
        </div>
        <div class="form-group" style="flex: 1; min-width: 200px; margin: 0;">
            <label for="end_date" class="form-label">Tot Datum</label>
            <input type="date" id="end_date" name="end_date" class="form-input" value="{{ $endDate->format('Y-m-d') }}">
        </div>
        <button type="submit" class="neu-button-primary">
            <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
            </svg>
            Toon
        </button>
    </form>
</div>

@if($cleanerStats->count() > 0)
<!-- Cleaner Performance Summary -->
<div class="neu-card" style="margin-bottom: 24px;">
    <h3 class="card-title">Prestatie Overzicht per Schoonmaker</h3>
    <p style="color: var(--neu-text-secondary); font-size: 14px; margin-top: 8px;">
        Periode: {{ $startDate->format('d-m-Y') }} t/m {{ $endDate->format('d-m-Y') }}
    </p>

    <div style="margin-top: 24px;">
        @foreach($cleanerStats as $stat)
        <div class="neu-widget" style="margin-bottom: 16px;">
            <div class="widget-body" style="padding: 20px;">
                <!-- Cleaner Name and Overall Performance -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <div>
                        <h4 style="font-size: 18px; font-weight: 600; color: var(--neu-text-primary); margin-bottom: 4px;">
                            {{ $stat['cleaner']->user->name }}
                        </h4>
                        <p style="color: var(--neu-text-secondary); font-size: 14px;">
                            {{ $stat['total_tasks'] }} taken voltooid
                        </p>
                    </div>
                    <div style="text-align: right;">
                        @if($stat['performance'] === 'faster')
                            <span class="neu-badge success" style="font-size: 16px; padding: 8px 16px;">
                                {{ abs($stat['variance_minutes']) }}min sneller
                            </span>
                        @elseif($stat['performance'] === 'slower')
                            <span class="neu-badge danger" style="font-size: 16px; padding: 8px 16px;">
                                {{ $stat['variance_minutes'] }}min langzamer
                            </span>
                        @else
                            <span class="neu-badge" style="font-size: 16px; padding: 8px 16px;">
                                Exact op tijd
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Statistics Grid -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px; margin-bottom: 16px;">
                    <div style="padding: 12px; background: var(--neu-bg-light); border-radius: 10px;">
                        <div style="font-size: 12px; color: var(--neu-text-secondary); margin-bottom: 4px;">Gepland Totaal</div>
                        <div style="font-size: 20px; font-weight: 600; color: var(--neu-text-primary);">
                            {{ $stat['total_planned_minutes'] }}min
                        </div>
                    </div>

                    <div style="padding: 12px; background: var(--neu-bg-light); border-radius: 10px;">
                        <div style="font-size: 12px; color: var(--neu-text-secondary); margin-bottom: 4px;">Werkelijk Totaal</div>
                        <div style="font-size: 20px; font-weight: 600; color: var(--neu-text-primary);">
                            {{ $stat['total_actual_minutes'] }}min
                        </div>
                    </div>

                    <div style="padding: 12px; background: var(--neu-bg-light); border-radius: 10px;">
                        <div style="font-size: 12px; color: var(--neu-text-secondary); margin-bottom: 4px;">Verschil</div>
                        <div style="font-size: 20px; font-weight: 600; color: {{ $stat['variance_minutes'] < 0 ? '#10b981' : '#ef4444' }};">
                            {{ $stat['variance_minutes'] > 0 ? '+' : '' }}{{ $stat['variance_minutes'] }}min
                        </div>
                    </div>

                    <div style="padding: 12px; background: var(--neu-bg-light); border-radius: 10px;">
                        <div style="font-size: 12px; color: var(--neu-text-secondary); margin-bottom: 4px;">Percentage</div>
                        <div style="font-size: 20px; font-weight: 600; color: {{ $stat['variance_percent'] < 0 ? '#10b981' : '#ef4444' }};">
                            {{ $stat['variance_percent'] > 0 ? '+' : '' }}{{ $stat['variance_percent'] }}%
                        </div>
                    </div>
                </div>

                <!-- Task Breakdown -->
                <div style="display: flex; gap: 16px; flex-wrap: wrap;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="width: 12px; height: 12px; background: #10b981; border-radius: 50%;"></span>
                        <span style="font-size: 14px; color: var(--neu-text-secondary);">
                            {{ $stat['faster_count'] }} sneller
                        </span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="width: 12px; height: 12px; background: #ef4444; border-radius: 50%;"></span>
                        <span style="font-size: 14px; color: var(--neu-text-secondary);">
                            {{ $stat['slower_count'] }} langzamer
                        </span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="width: 12px; height: 12px; background: #6b7280; border-radius: 50%;"></span>
                        <span style="font-size: 14px; color: var(--neu-text-secondary);">
                            {{ $stat['exact_count'] }} exact
                        </span>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Detailed Task List -->
<div class="neu-card">
    <h3 class="card-title">Gedetailleerde Takenlijst</h3>

    <div class="table-container">
        <table class="neu-table">
            <thead>
                <tr>
                    <th>Datum</th>
                    <th>Kamer</th>
                    <th>Schoonmaker</th>
                    <th>Gepland</th>
                    <th>Werkelijk</th>
                    <th>Verschil</th>
                    <th>Prestatie</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tasksWithMetrics as $item)
                    @php
                        $task = $item['task'];
                        $variance = $item['variance'];
                        $variancePercent = $item['variance_percent'];
                        $performance = $item['performance'];
                    @endphp
                    <tr>
                        <td>{{ $task->date->format('d-m-Y') }}</td>
                        <td><strong>{{ $task->room->room_number }}</strong></td>
                        <td>{{ $task->cleaner->user->name }}</td>
                        <td>{{ $task->planned_duration }}min</td>
                        <td>{{ $task->actual_duration }}min</td>
                        <td style="color: {{ $variance < 0 ? '#10b981' : ($variance > 0 ? '#ef4444' : '#6b7280') }}; font-weight: 600;">
                            {{ $variance > 0 ? '+' : '' }}{{ $variance }}min
                        </td>
                        <td>
                            @if($performance === 'faster')
                                <span class="neu-badge success">
                                    {{ abs($variancePercent) }}% sneller
                                </span>
                            @elseif($performance === 'slower')
                                <span class="neu-badge danger">
                                    {{ $variancePercent }}% langzamer
                                </span>
                            @else
                                <span class="neu-badge">
                                    Exact
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@else
<!-- Empty State -->
<div class="neu-empty-state">
    <div class="empty-icon">
        <svg width="48" height="48" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
        </svg>
    </div>
    <div class="empty-title">Geen voltooide taken gevonden</div>
    <div class="empty-message">Er zijn geen voltooide taken in de geselecteerde periode. Taken moeten worden afgerond om prestaties te kunnen analyseren.</div>
</div>
@endif

@endsection
