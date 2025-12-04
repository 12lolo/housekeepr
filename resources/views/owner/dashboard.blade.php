@extends('layouts.app-neu')

@section('title', 'Owner Dashboard')

@php
    $portalName = 'Owner Portal';
@endphp

@section('page-title', $hotel->name)

@section('nav')
    <a href="{{ route('owner.dashboard') }}" class="active">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
        </svg>
        Dashboard
    </a>
    <a href="{{ route('owner.rooms.index') }}">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
        </svg>
        Kamers
    </a>
    <a href="{{ route('owner.bookings.index') }}">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
        </svg>
        Boekingen
    </a>
    <a href="{{ route('owner.cleaners.index') }}">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
        </svg>
        Schoonmakers
    </a>
    <a href="{{ route('owner.issues.index') }}">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
        Problemen
    </a>
@endsection

@section('content')
<div class="owner-dashboard">
    <!-- Urgent Issues Alert -->
    @if($urgent_issues->count() > 0)
        <div class="neu-alert danger urgent-alert">
            <div class="alert-icon">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="alert-content">
                <div class="alert-title">Urgente Problemen</div>
                <div class="alert-message">Er zijn {{ $urgent_issues->count() }} kamer(s) die niet gebruikt kunnen worden!</div>
            </div>
        </div>
    @endif

    <!-- Dashboard Stats Grid -->
    <div class="neu-dashboard-grid">
        <!-- Total Rooms -->
        <div class="neu-stat-card">
            <div class="stat-icon">
                <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                </svg>
            </div>
            <div class="stat-label">Totaal Kamers</div>
            <div class="stat-value">{{ $stats['total_rooms'] }}</div>
        </div>

        <!-- Tasks Today -->
        <div class="neu-stat-card">
            <div class="stat-icon">
                <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="stat-label">Taken Vandaag</div>
            <div class="stat-value">{{ $stats['tasks_today'] }}</div>
        </div>

        <!-- Tasks Pending -->
        <div class="neu-stat-card">
            <div class="stat-icon">
                <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="stat-label">Nog Te Doen</div>
            <div class="stat-value">{{ $stats['tasks_pending'] }}</div>
        </div>

        <!-- Open Issues -->
        @if($stats['open_issues'] > 0)
        <div class="neu-stat-card">
            <div class="stat-icon">
                <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="stat-label">Open Problemen</div>
            <div class="stat-value">{{ $stats['open_issues'] }}</div>
        </div>
        @endif
    </div>

    <!-- Today's Tasks Widget -->
    <div class="neu-widget today-tasks-widget">
        <div class="widget-header">
            <h3>Taken Vandaag</h3>
            <button class="neu-button-secondary compact-button">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                </svg>
                Nieuwe Taak
            </button>
        </div>
        <div class="widget-body">
            @if($today_tasks->count() > 0)
                <div class="neu-table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Kamer</th>
                                <th>Schoonmaker</th>
                                <th>Start Tijd</th>
                                <th>Check-in</th>
                                <th>Status</th>
                                <th>Acties</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($today_tasks as $task)
                            <tr>
                                <td><strong>{{ $task->room->room_number }}</strong></td>
                                <td>{{ $task->cleaner->user->name }}</td>
                                <td>{{ $task->suggested_start_time ? $task->suggested_start_time->format('H:i') : '-' }}</td>
                                <td>{{ $task->deadline->format('H:i') }}</td>
                                <td>
                                    @if($task->status === 'completed')
                                        <span class="neu-badge success">Klaar</span>
                                    @elseif($task->status === 'in_progress')
                                        <span class="neu-badge warning">Bezig</span>
                                    @else
                                        <span class="neu-badge">Te doen</span>
                                    @endif
                                </td>
                                <td class="table-actions">
                                    <button aria-label="View">
                                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </button>
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
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="empty-title">Geen taken vandaag</div>
                    <div class="empty-message">Er zijn geen schoonmaaktaken gepland voor vandaag.</div>
                </div>
            @endif
        </div>
    </div>

    <!-- Urgent Issues Widget -->
    @if($urgent_issues->count() > 0)
    <div class="neu-widget urgent-issues-widget">
        <div class="widget-header">
            <h3>Urgente Problemen</h3>
            <span class="neu-badge danger">{{ $urgent_issues->count() }} Open</span>
        </div>
        <div class="widget-body">
            <div class="neu-table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Kamer</th>
                            <th>Probleem</th>
                            <th>Gemeld op</th>
                            <th>Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($urgent_issues as $issue)
                        <tr>
                            <td><strong>{{ $issue->room->room_number }}</strong></td>
                            <td>{{ $issue->note }}</td>
                            <td>{{ $issue->created_at->format('d-m-Y H:i') }}</td>
                            <td class="table-actions">
                                <button class="neu-button-success resolve-button">
                                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20" class="icon-inline">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Gefixt
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Quick Actions -->
    <div class="quick-actions-section">
        <h3 class="section-title">Snelle Acties</h3>
        <div class="neu-button-group actions-grid">
            <button class="neu-button-primary">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" class="icon-inline">
                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                </svg>
                Voeg Kamer Toe
            </button>
            <button class="neu-button-secondary">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" class="icon-inline">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                </svg>
                Nieuwe Boeking
            </button>
            <button class="neu-button-secondary">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" class="icon-inline">
                    <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"/>
                </svg>
                Nieuwe Schoonmaker
            </button>
            <button class="neu-button-secondary">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" class="icon-inline">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                Rapportages
            </button>
        </div>
    </div>
</div>
@endsection
