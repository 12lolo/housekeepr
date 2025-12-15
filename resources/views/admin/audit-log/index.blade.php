@extends('layouts.app-neu')

@php
    $portalName = 'Admin';
@endphp

@section('page-title', 'Audit Log')

@section('nav')
    <a href="{{ route('admin.dashboard') }}">
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
    <a href="{{ route('admin.audit-log.index') }}" class="active">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 0l-2 2a1 1 0 101.414 1.414L8 10.414l1.293 1.293a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        Audit Log
    </a>
@endsection

@section('content')
    <div class="neu-widget">
        <div class="widget-header">
            <h2>Audit Log</h2>
        </div>
        <div class="widget-body">
            <form method="GET" action="{{ route('admin.audit-log.index') }}" class="audit-log-filters">
                <div class="filter-grid">
                    <div class="neu-form-group">
                        <label for="user_id" class="neu-label">Gebruiker</label>
                        <select id="user_id" name="user_id" class="neu-input">
                            <option value="">Alle gebruikers</option>
                            @foreach($causers as $causer)
                                <option value="{{ $causer->id }}" {{ request('user_id') == $causer->id ? 'selected' : '' }}>
                                    {{ $causer->name ?? $causer->email }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="neu-form-group">
                        <label for="event" class="neu-label">Type Actie</label>
                        <select id="event" name="event" class="neu-input">
                            <option value="">Alle acties</option>
                            <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>Aangemaakt</option>
                            <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>Bijgewerkt</option>
                            <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>Verwijderd</option>
                        </select>
                    </div>

                    <div class="neu-form-group">
                        <label for="from_date" class="neu-label">Van Datum</label>
                        <input
                            type="date"
                            id="from_date"
                            name="from_date"
                            class="neu-input"
                            value="{{ request('from_date') }}"
                        >
                    </div>

                    <div class="neu-form-group">
                        <label for="to_date" class="neu-label">Tot Datum</label>
                        <input
                            type="date"
                            id="to_date"
                            name="to_date"
                            class="neu-input"
                            value="{{ request('to_date') }}"
                        >
                    </div>

                    <div class="neu-form-group">
                        <label for="search" class="neu-label">Zoeken</label>
                        <input
                            type="text"
                            id="search"
                            name="search"
                            class="neu-input"
                            placeholder="Zoek in beschrijving..."
                            value="{{ request('search') }}"
                        >
                    </div>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="neu-button-primary">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" class="icon-inline">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                        </svg>
                        Filteren
                    </button>
                    @if(request()->hasAny(['user_id', 'event', 'from_date', 'to_date', 'search']))
                        <a href="{{ route('admin.audit-log.index') }}" class="neu-button-secondary">
                            Filters Wissen
                        </a>
                    @endif
                </div>
            </form>

            @if($activities->count() > 0)
                <div class="neu-table-wrapper audit-log-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Tijdstip</th>
                                <th>Gebruiker</th>
                                <th>Actie</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activities as $activity)
                            <tr>
                                <td class="timestamp-col">
                                    {{ $activity->created_at->format('d-m-Y H:i:s') }}
                                </td>
                                <td class="user-col">
                                    {{ $activity->causer?->name ?? 'Systeem' }}
                                </td>
                                <td>
                                    @if($activity->event === 'created')
                                        <span class="neu-badge success">Aangemaakt</span>
                                    @elseif($activity->event === 'updated')
                                        <span class="neu-badge warning">Bijgewerkt</span>
                                    @elseif($activity->event === 'deleted')
                                        <span class="neu-badge danger">Verwijderd</span>
                                    @elseif($activity->event)
                                        <span class="neu-badge">{{ ucfirst($activity->event) }}</span>
                                    @else
                                        <span class="neu-badge" style="background-color: #e5e7eb; color: #6b7280;">Actie</span>
                                    @endif
                                </td>
                                <td class="description-col">
                                    <div>
                                        {{ $activity->description }}
                                        @if($activity->subject)
                                            <div class="subject-info">
                                                {{ class_basename($activity->subject_type) }} #{{ $activity->subject_id }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $activities->withQueryString()->links() }}
                </div>
            @else
                <div class="neu-empty-state">
                    <div class="empty-icon">
                        <svg width="48" height="48" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 0l-2 2a1 1 0 101.414 1.414L8 10.414l1.293 1.293a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="empty-title">Geen activiteiten gevonden</div>
                    <div class="empty-message">Er zijn geen activiteiten die voldoen aan de filters</div>
                </div>
            @endif
        </div>
    </div>
@endsection
