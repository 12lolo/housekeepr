@extends('layouts.app-neu')

@php
    $portalName = 'Admin';
@endphp

@section('page-title', $owner->name)

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
            <h2>Eigenaar Details</h2>
            <div class="d-flex gap-3">
                <a href="{{ route('admin.owners.edit', $owner) }}" class="neu-button-primary">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" class="icon-inline">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                    </svg>
                    Bewerken
                </a>
                <a href="{{ route('admin.owners.index') }}" class="neu-button-secondary">Terug</a>
            </div>
        </div>
        <div class="widget-body">
            <div class="info-grid mb-8">
                <div class="info-field">
                    <div class="info-label">Naam</div>
                    <div class="info-value">{{ $owner->name }}</div>
                </div>
                <div class="info-field">
                    <div class="info-label">Email</div>
                    <div class="info-value">{{ $owner->email }}</div>
                </div>
                <div class="info-field">
                    <div class="info-label">Status</div>
                    <div>
                        @if($owner->status === 'active')
                            <span class="neu-badge success">Actief</span>
                        @elseif($owner->status === 'pending')
                            <span class="neu-badge warning">Pending</span>
                        @else
                            <span class="neu-badge">Gedeactiveerd</span>
                        @endif
                    </div>
                </div>
                <div class="info-field">
                    <div class="info-label">Aangemaakt</div>
                    <div class="info-value">{{ $owner->created_at->format('d-m-Y H:i') }}</div>
                </div>
            </div>
        </div>
    </div>

    @if($owner->hotel)
    <div class="neu-widget mt-6">
        <div class="widget-header">
            <h2>Hotel Informatie</h2>
        </div>
        <div class="widget-body">
            <div class="info-grid">
                <div class="info-field">
                    <div class="info-label">Hotelnaam</div>
                    <div class="info-value">{{ $owner->hotel->name }}</div>
                </div>
                <div class="info-field">
                    <div class="info-label">Aantal Kamers</div>
                    <div class="info-value">{{ $owner->hotel->rooms->count() }}</div>
                </div>
                <div class="info-field">
                    <div class="info-label">Aantal Schoonmakers</div>
                    <div class="info-value">{{ $owner->hotel->cleaners->count() }}</div>
                </div>
            </div>
        </div>
    </div>

    @if($owner->hotel->rooms->count() > 0)
    <div class="neu-widget mt-6">
        <div class="widget-header">
            <h2>Kamers</h2>
        </div>
        <div class="widget-body">
            <div class="neu-table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Kamernummer</th>
                            <th>Standaard Duur</th>
                            <th>Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($owner->hotel->rooms as $room)
                        <tr>
                            <td><strong>{{ $room->room_number }}</strong></td>
                            <td>{{ $room->standard_duration }} min</td>
                            <td>{{ ucfirst($room->room_type) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    @if($owner->hotel->cleaners->count() > 0)
    <div class="neu-widget mt-6">
        <div class="widget-header">
            <h2>Schoonmakers</h2>
        </div>
        <div class="widget-body">
            <div class="neu-table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Naam</th>
                            <th>Email</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($owner->hotel->cleaners as $cleaner)
                        <tr>
                            <td><strong>{{ $cleaner->user->name }}</strong></td>
                            <td>{{ $cleaner->user->email }}</td>
                            <td>
                                @if($cleaner->status === 'active')
                                    <span class="neu-badge success">Actief</span>
                                @else
                                    <span class="neu-badge">Gedeactiveerd</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
    @endif
@endsection
