@extends('layouts.app-neu')

@section('title', 'Admin Dashboard')

@php
    $portalName = 'Admin Portal';
@endphp

@section('page-title', 'Dashboard')

@section('nav')
    <a href="{{ route('admin.dashboard') }}" class="active">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
        </svg>
        Dashboard
    </a>
    <a href="{{ route('admin.owners.index') }}">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
        </svg>
        Eigenaren
    </a>
    <a href="{{ route('admin.audit-log.index') }}">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
        </svg>
        Audit Log
    </a>
@endsection

@section('content')
    <!-- Dashboard Stats Grid -->
    <div class="neu-dashboard-grid">
        <!-- Total Hotels -->
        <div class="neu-stat-card">
            <div class="stat-icon">
                <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                </svg>
            </div>
            <div class="stat-label">Totaal Hotels</div>
            <div class="stat-value">{{ $stats['total_hotels'] }}</div>
        </div>

        <!-- Total Owners -->
        <div class="neu-stat-card">
            <div class="stat-icon">
                <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                </svg>
            </div>
            <div class="stat-label">Eigenaren</div>
            <div class="stat-value">{{ $stats['total_owners'] }}</div>
        </div>

        <!-- Total Cleaners -->
        <div class="neu-stat-card">
            <div class="stat-icon">
                <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                </svg>
            </div>
            <div class="stat-label">Schoonmakers</div>
            <div class="stat-value">{{ $stats['total_cleaners'] }}</div>
        </div>

        <!-- Pending Owners (if any) -->
        @if($stats['pending_owners'] > 0)
        <div class="neu-stat-card">
            <div class="stat-icon">
                <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="stat-label">Pending Eigenaren</div>
            <div class="stat-value">{{ $stats['pending_owners'] }}</div>
        </div>
        @endif
    </div>

    <!-- Recent Hotels Widget -->
    <div class="neu-widget">
        <div class="widget-header">
            <h3>Recente Hotels</h3>
            <button class="neu-button-secondary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" style="display: inline-block; vertical-align: middle; margin-right: 0.25rem;">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                </svg>
                Nieuw Hotel
            </button>
        </div>
        <div class="widget-body">
            @if($recent_hotels->count() > 0)
                <div class="neu-table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Hotel Naam</th>
                                <th>Eigenaar</th>
                                <th>Email</th>
                                <th>Aangemaakt</th>
                                <th>Acties</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recent_hotels as $hotel)
                            <tr>
                                <td style="font-weight: 600;">{{ $hotel->name }}</td>
                                <td>{{ $hotel->owner->name }}</td>
                                <td style="color: var(--neu-light-text-muted, #718096);">{{ $hotel->owner->email }}</td>
                                <td>{{ $hotel->created_at->format('d-m-Y') }}</td>
                                <td class="table-actions">
                                    <button aria-label="View">
                                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </button>
                                    <button aria-label="Edit">
                                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
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
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                        </svg>
                    </div>
                    <div class="empty-title">Nog geen hotels</div>
                    <div class="empty-message">Er zijn nog geen hotels aangemaakt in het systeem.</div>
                    <button class="neu-button-primary" style="margin-top: 1rem;">
                        Voeg eerste hotel toe
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Quick Actions -->
    <div style="margin-top: 2rem;">
        <h3 style="margin-bottom: 1rem; font-size: 1.125rem; font-weight: 600; color: var(--neu-light-text, #4a5568);">Snelle Acties</h3>
        <div class="neu-button-group">
            <button class="neu-button-primary">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" style="display: inline-block; vertical-align: middle; margin-right: 0.5rem;">
                    <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"/>
                </svg>
                Nodig Eigenaar Uit
            </button>
            <button class="neu-button-secondary">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" style="display: inline-block; vertical-align: middle; margin-right: 0.5rem;">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                </svg>
                Bekijk Audit Log
            </button>
            <button class="neu-button-secondary">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" style="display: inline-block; vertical-align: middle; margin-right: 0.5rem;">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                </svg>
                Systeem Instellingen
            </button>
        </div>
    </div>
@endsection
