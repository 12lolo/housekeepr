@extends('layouts.app-neu')

@section('title', 'Owner Dashboard')

@php
    $portalName = 'Owner Portal';
@endphp

@section('page-title', $hotel->name)

@section('nav')
    <a href="javascript:void(0)" class="nav-section-link active" data-section="dashboard" onclick="event.preventDefault();">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
        </svg>
        Dashboard
    </a>
    <a href="javascript:void(0)" class="nav-section-link" data-section="kamers" onclick="event.preventDefault();">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
        </svg>
        Kamers
    </a>
    <a href="javascript:void(0)" class="nav-section-link" data-section="boekingen" onclick="event.preventDefault();">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
        </svg>
        Boekingen
    </a>
    <a href="javascript:void(0)" class="nav-section-link" data-section="schoonmakers" onclick="event.preventDefault();">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
        </svg>
        Schoonmakers
    </a>
    <a href="javascript:void(0)" class="nav-section-link" data-section="problemen" onclick="event.preventDefault();">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
        Problemen
    </a>
    <a href="javascript:void(0)" class="nav-section-link" data-section="schoonmaakplanning" onclick="event.preventDefault();">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
        </svg>
        Schoonmaakplanning
    </a>
@endsection

@section('content')
<div class="neu-accordion-container">
    <!-- Dashboard Section (Open by default) -->
    <div class="neu-accordion-section" id="section-dashboard">
        <button class="neu-accordion-header active" data-section="dashboard">
            <div class="neu-accordion-header-content">
                <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                </svg>
                <span>Dashboard</span>
            </div>
            <svg class="neu-accordion-icon" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
        </button>

        <div class="neu-accordion-content active">
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
                <div class="neu-stat-card highlight">
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

            <!-- Today's Tasks -->
            <div class="neu-card mt-6">
                <h2 class="card-title">Taken Vandaag</h2>
                <div class="table-container">
                    <table class="neu-table">
                        <thead>
                            <tr>
                                <th>Kamer</th>
                                <th>Schoonmaker</th>
                                <th>Start Tijd</th>
                                <th>Check-in</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($today_tasks as $task)
                                <tr>
                                    <td><strong>{{ $task->room->room_number }}</strong></td>
                                    <td>{{ $task->cleaner->user->name ?? '-' }}</td>
                                    <td>{{ $task->suggested_start_time ? $task->suggested_start_time->format('H:i') : '-' }}</td>
                                    <td>{{ $task->deadline->format('H:i') }}</td>
                                    <td>
                                        @if($task->status === 'completed')
                                            <span class="badge badge-success">Klaar</span>
                                        @elseif($task->status === 'in_progress')
                                            <span class="badge badge-warning">Bezig</span>
                                        @else
                                            <span class="badge badge-secondary">Te doen</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Geen taken vandaag</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Kamers Section -->
    <div class="neu-accordion-section" id="section-kamers">
        <button class="neu-accordion-header" data-section="kamers">
            <div class="neu-accordion-header-content">
                <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                </svg>
                <span>Kamers</span>
            </div>
            <svg class="neu-accordion-icon" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
        </button>

        <div class="neu-accordion-content">
            <div class="neu-card">
                <div class="card-header-actions">
                    <h2 class="card-title">Alle Kamers</h2>
                    <button id="addRoomBtn" class="neu-button-primary tour-target" onclick="openModal('roomCreateModal')">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                        </svg>
                        Nieuwe Kamer
                    </button>
                </div>

                <div class="table-container">
                    <table class="neu-table">
                        <thead>
                            <tr>
                                <th>Nummer</th>
                                <th>Type</th>
                                <th>Schoonmaaktijd</th>
                                <th>Check-out</th>
                                <th>Check-in</th>
                                <th>Acties</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rooms as $room)
                                <tr>
                                    <td><strong>{{ $room->room_number }}</strong></td>
                                    <td>
                                        @php
                                            $typeLabels = [
                                                'single' => 'Eenpersoons',
                                                'double' => 'Tweepersoons',
                                                'suite' => 'Suite',
                                                'family' => 'Familie'
                                            ];
                                            echo $typeLabels[$room->room_type] ?? ucfirst($room->room_type ?? 'Onbekend');
                                        @endphp
                                    </td>
                                    <td>{{ $room->standard_duration ?? 30 }} min</td>
                                    <td>{{ $room->checkout_time ?? '11:00' }}</td>
                                    <td>{{ $room->checkin_time ?? '15:00' }}</td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-btn" aria-label="Bewerken"
                                                    data-room-id="{{ $room->id }}"
                                                    data-room-number="{{ $room->room_number }}"
                                                    data-room-type="{{ $room->room_type ?? '' }}"
                                                    data-standard-duration="{{ $room->standard_duration ?? 30 }}"
                                                    data-checkout-time="{{ $room->checkout_time ?? '11:00' }}"
                                                    data-checkin-time="{{ $room->checkin_time ?? '15:00' }}"
                                                    onclick="editRoomFromButton(this)">
                                                <svg width="18" height="18" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                                </svg>
                                            </button>
                                            <button class="action-btn delete-btn" aria-label="Verwijderen"
                                                    data-room-id="{{ $room->id }}"
                                                    data-room-number="{{ $room->room_number }}"
                                                    onclick="confirmDeleteRoomFromButton(this)">
                                                <svg width="18" height="18" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Geen kamers gevonden</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Boekingen Section -->
    <div class="neu-accordion-section" id="section-boekingen">
        <button class="neu-accordion-header" data-section="boekingen">
            <div class="neu-accordion-header-content">
                <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                </svg>
                <span>Boekingen</span>
            </div>
            <svg class="neu-accordion-icon" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
        </button>

        <div class="neu-accordion-content">
            <div class="neu-card">
                <div class="card-header-actions">
                    <h2 class="card-title">Alle Boekingen</h2>
                    <button id="addBookingBtn" class="neu-button-primary tour-target" onclick="openModal('bookingCreateModal')">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                        </svg>
                        Nieuwe Boeking
                    </button>
                </div>

                <div class="table-container">
                    <table class="neu-table">
                        <thead>
                            <tr>
                                <th>Kamer</th>
                                <th>Gast Naam</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th>Status</th>
                                <th>Acties</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $booking)
                                <tr>
                                    <td><strong>{{ $booking->room->room_number }}</strong></td>
                                    <td>{{ $booking->guest_name }}</td>
                                    <td>{{ $booking->check_in->format('d-m-Y') }}</td>
                                    <td>{{ $booking->check_out->format('d-m-Y') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $booking->status === 'confirmed' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-btn" aria-label="Bewerken" onclick="editBooking({{ $booking->id }}, {{ $booking->room_id }}, '{{ addslashes($booking->guest_name) }}', '{{ $booking->check_in }}', '{{ $booking->check_out }}', '{{ addslashes($booking->notes ?? '') }}')">
                                                <svg width="18" height="18" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Geen boekingen gevonden</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Schoonmakers Section -->
    <div class="neu-accordion-section" id="section-schoonmakers">
        <button class="neu-accordion-header" data-section="schoonmakers">
            <div class="neu-accordion-header-content">
                <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                </svg>
                <span>Schoonmakers</span>
            </div>
            <svg class="neu-accordion-icon" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
        </button>

        <div class="neu-accordion-content">
            <div class="neu-card">
                <div class="card-header-actions">
                    <h2 class="card-title">Alle Schoonmakers</h2>
                    <button id="addCleanerBtn" class="neu-button-primary tour-target" onclick="openModal('cleanerCreateModal')">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                        </svg>
                        Nieuwe Schoonmaker
                    </button>
                </div>

                <div class="table-container">
                    <table class="neu-table">
                        <thead>
                            <tr>
                                <th>Naam</th>
                                <th>Email</th>
                                <th>Telefoon</th>
                                <th>Status</th>
                                <th>Acties</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cleaners as $cleaner)
                                <tr>
                                    <td><strong>{{ $cleaner->user->name }}</strong></td>
                                    <td>{{ $cleaner->user->email }}</td>
                                    <td>{{ $cleaner->phone ?? '-' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $cleaner->status === 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($cleaner->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <!-- No actions for cleaners yet -->
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Geen schoonmakers gevonden</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Problemen Section -->
    <div class="neu-accordion-section" id="section-problemen">
        <button class="neu-accordion-header" data-section="problemen">
            <div class="neu-accordion-header-content">
                <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <span>Problemen</span>
            </div>
            <svg class="neu-accordion-icon" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
        </button>

        <div class="neu-accordion-content">
            <div class="neu-card">
                <div class="card-header-actions">
                    <h2 class="card-title">Alle Problemen</h2>
                    <button class="neu-button-primary" onclick="openModal('issueCreateModal')">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                        </svg>
                        Nieuw Probleem
                    </button>
                </div>

                <div class="table-container">
                    <table class="neu-table">
                        <thead>
                            <tr>
                                <th>Kamer</th>
                                <th>Probleem</th>
                                <th>Impact</th>
                                <th>Status</th>
                                <th>Gemeld op</th>
                                <th>Acties</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($issues as $issue)
                                <tr>
                                    <td><strong>{{ $issue->room->room_number }}</strong></td>
                                    <td>{{ $issue->note }}</td>
                                    <td>
                                        <span class="badge badge-{{ $issue->impact === 'kan_niet_gebruikt' ? 'danger' : 'warning' }}">
                                            {{ $issue->impact === 'kan_niet_gebruikt' ? 'Urgent' : 'Normaal' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $issue->status === 'open' ? 'warning' : 'success' }}">
                                            {{ ucfirst($issue->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $issue->created_at->format('d-m-Y H:i') }}</td>
                                    <td>
                                        <div class="action-buttons">
                                            @if($issue->status === 'open')
                                            <form action="{{ route('owner.issues.mark-fixed', $issue) }}" method="POST" onsubmit="return handleMarkFixed(event, this)">
                                                @csrf
                                                <button type="submit" class="action-btn" aria-label="Als Gefixt Markeren">
                                                    <svg width="18" height="18" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Geen problemen gevonden</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Schoonmaakplanning Section -->
    <div class="neu-accordion-section" id="section-schoonmaakplanning">
        <button class="neu-accordion-header" data-section="schoonmaakplanning">
            <div class="neu-accordion-header-content">
                <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                </svg>
                <span>Schoonmaakplanning</span>
            </div>
            <svg class="neu-accordion-icon" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
        </button>

        <div class="neu-accordion-content">
            <div class="neu-card">
                <div class="card-header-actions">
                    <h2 class="card-title">Schoonmaakplanning</h2>
                </div>

                <div class="table-container">
                    <table class="neu-table">
                        <thead>
                            <tr>
                                <th>Kamer</th>
                                <th>Datum</th>
                                <th>Beschikbaar voor schoonmaak</th>
                                <th>Deadline</th>
                                <th>Schoonmaker</th>
                                <th>Status</th>
                                <th>Acties</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cleaning_schedule as $task)
                                <tr>
                                    <td><strong>{{ $task->room->room_number }}</strong></td>
                                    <td>{{ $task->date->format('d-m-Y') }}</td>
                                    <td>
                                        @if($task->suggested_start_time)
                                            {{ $task->suggested_start_time->format('H:i') }}
                                        @else
                                            <span class="text-muted">Niet gepland</span>
                                        @endif
                                    </td>
                                    <td>{{ $task->deadline->format('H:i') }}</td>
                                    <td>
                                        @if($task->cleaner)
                                            {{ $task->cleaner->user->name }}
                                        @else
                                            <span class="text-muted">Niet toegewezen</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $task->status === 'completed' ? 'success' : ($task->status === 'in_progress' ? 'info' : 'warning') }}">
                                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <!-- No actions for tasks yet -->
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Geen schoonmaaktaken gevonden</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Fallback modal functions if modal-manager.js doesn't load
if (typeof window.openModal !== 'function') {
    console.warn('modal-manager.js not loaded, using fallback functions');

    window.openModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
            console.log('Opened modal:', modalId);
        } else {
            console.error('Modal not found:', modalId);
        }
    };

    window.closeModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
            console.log('Closed modal:', modalId);
        }
    };
}

// Always define these functions (not conditional on modal-manager.js)

// Wrapper function to get data from button and call editRoom
window.editRoomFromButton = function(button) {
    const roomId = button.getAttribute('data-room-id');
    const roomNumber = button.getAttribute('data-room-number');
    const roomType = button.getAttribute('data-room-type');
    const standardDuration = button.getAttribute('data-standard-duration');
    const checkoutTime = button.getAttribute('data-checkout-time');
    const checkinTime = button.getAttribute('data-checkin-time');

    console.log('editRoomFromButton called');
    editRoom(roomId, roomNumber, roomType, standardDuration, checkoutTime, checkinTime);
};

window.editRoom = function(roomId, roomNumber, roomType, standardDuration, checkoutTime, checkinTime) {
    console.log('editRoom called with:', { roomId, roomNumber, roomType, standardDuration, checkoutTime, checkinTime });

    const form = document.getElementById('roomEditForm');
    if (!form) {
        console.error('Room edit form not found');
        return;
    }
    form.action = `/owner/rooms/${roomId}`;

    // Fill in the form fields
    const roomNumberInput = document.getElementById('edit_room_number');
    const roomTypeSelect = document.getElementById('edit_room_type');
    const durationInput = document.getElementById('edit_standard_duration');
    const checkoutInput = document.getElementById('edit_checkout_time');
    const checkinInput = document.getElementById('edit_checkin_time');

    if (roomNumberInput) roomNumberInput.value = roomNumber || '';
    if (roomTypeSelect) roomTypeSelect.value = roomType || '';
    if (durationInput) durationInput.value = standardDuration || 30;
    if (checkoutInput) checkoutInput.value = checkoutTime || '11:00';
    if (checkinInput) checkinInput.value = checkinTime || '15:00';

    console.log('Form filled, opening modal...');
    window.openModal('roomEditModal');
};

window.editBooking = function(bookingId, roomId, guestName, checkIn, checkOut, notes) {
    const form = document.getElementById('bookingEditForm');
    if (!form) {
        console.error('Booking edit form not found');
        return;
    }
    form.action = `/owner/bookings/${bookingId}`;

    document.getElementById('edit_booking_room_id').value = roomId;
    document.getElementById('edit_guest_name').value = guestName;
    document.getElementById('edit_check_in').value = checkIn;
    document.getElementById('edit_check_out').value = checkOut;
    document.getElementById('edit_notes').value = notes || '';

    window.openModal('bookingEditModal');
};

// Wrapper function to get data from button and call confirmDeleteRoom
window.confirmDeleteRoomFromButton = function(button) {
    const roomId = button.getAttribute('data-room-id');
    const roomNumber = button.getAttribute('data-room-number');

    console.log('confirmDeleteRoomFromButton called');
    confirmDeleteRoom(roomId, roomNumber);
};

window.confirmDeleteRoom = function(roomId, roomNumber) {
    if (confirm(`Weet je zeker dat je kamer ${roomNumber} wilt verwijderen?\n\nDit kan niet ongedaan worden gemaakt.`)) {
        deleteRoom(roomId);
    }
};

window.deleteRoom = function(roomId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    fetch(`/owner/rooms/${roomId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        console.log('Delete response status:', response.status);
        console.log('Delete response headers:', response.headers);

        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            return response.text().then(text => {
                console.error('Expected JSON but got:', text.substring(0, 200));
                throw new Error('Server retourneerde geen JSON. Check de server logs.');
            });
        }

        if (!response.ok) {
            return response.json().then(data => {
                throw data;
            });
        }
        return response.json();
    })
    .then(data => {
        showToast(data.message || 'Kamer verwijderd.', 'success');
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    })
    .catch(error => {
        console.error('Delete error:', error);
        if (error.message) {
            showToast(error.message, 'error', 5000);
        } else {
            showToast('Er is een fout opgetreden. Probeer opnieuw.', 'error', 5000);
        }
    });
};

// Form submission handler
function handleFormSubmit(form) {
    const formData = new FormData(form);
    const method = form.method || 'POST';
    const action = form.action;

    fetch(action, {
        method: method,
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => {
        if (response.ok) {
            closeModal();
            // Reload the page to show updated data
            window.location.reload();
        } else {
            return response.text().then(html => {
                // Show validation errors
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const content = doc.querySelector('.neu-portal-main') || doc.body;
                document.getElementById('modalBody').innerHTML = content.innerHTML;

                // Re-attach form handler
                const newForm = document.getElementById('modalBody').querySelector('form');
                if (newForm) {
                    newForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        handleFormSubmit(this);
                    });
                }
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}

// Handle mark as fixed form submission
function handleMarkFixed(event, form) {
    event.preventDefault();

    const formData = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (response.ok) {
            // Reload the page to show updated data
            window.location.reload();
        } else {
            return response.json().then(data => {
                alert(data.message || 'An error occurred. Please try again.');
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });

    return false;
}

// Close modal on overlay click
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('neu-modal-overlay') && e.target.classList.contains('active')) {
        window.closeModal(e.target.id);
    }
});

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const activeModal = document.querySelector('.neu-modal-overlay.active');
        if (activeModal) {
            window.closeModal(activeModal.id);
        }
    }
});

document.addEventListener('DOMContentLoaded', function() {
    // Debug: Check if modal functions are available
    console.log('openModal available:', typeof window.openModal === 'function');
    console.log('Modal panel exists:', document.getElementById('roomCreateModal') !== null);

    const accordionHeaders = document.querySelectorAll('.neu-accordion-header');
    const navSectionLinks = document.querySelectorAll('.nav-section-link');

    // Accordion header click handler
    accordionHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const section = this.closest('.neu-accordion-section');
            const content = section.querySelector('.neu-accordion-content');
            const isActive = this.classList.contains('active');
            const sectionName = this.getAttribute('data-section');

            // Close all sections
            document.querySelectorAll('.neu-accordion-header').forEach(h => {
                h.classList.remove('active');
            });
            document.querySelectorAll('.neu-accordion-content').forEach(c => {
                c.classList.remove('active');
            });

            // Update sidebar nav active state
            document.querySelectorAll('.nav-section-link').forEach(link => {
                link.classList.remove('active');
            });

            // If wasn't active, open it and scroll to top
            if (!isActive) {
                this.classList.add('active');
                content.classList.add('active');

                // Set corresponding sidebar link as active
                const correspondingNavLink = document.querySelector(`.nav-section-link[data-section="${sectionName}"]`);
                if (correspondingNavLink) {
                    correspondingNavLink.classList.add('active');
                }

                setTimeout(() => {
                    section.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }, 100);
            }
        });
    });

    // Sidebar navigation link click handler
    navSectionLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const sectionName = this.getAttribute('data-section');

            // Find and click the corresponding accordion header
            const targetHeader = document.querySelector(`.neu-accordion-header[data-section="${sectionName}"]`);
            if (targetHeader) {
                targetHeader.click();
            }
        });
    });
});
</script>

{{-- Modal Panels --}}
@include('owner.panels.room-create')
@include('owner.panels.room-edit')
@include('owner.panels.booking-create')
@include('owner.panels.booking-edit')
@include('owner.panels.cleaner-create')
@include('owner.panels.issue-create')

{{-- Toast Notification --}}
<div id="toast" class="toast" style="display: none;">
    <div class="toast-content">
        <span id="toast-message"></span>
    </div>
</div>

<style>
.action-btn.delete-btn {
    color: #ef4444;
}

.action-btn.delete-btn:hover {
    background: #fee2e2;
    color: #dc2626;
}

.toast {
    position: fixed;
    top: 20px;
    right: 20px;
    background: var(--surface-color, #fff);
    color: var(--text-color, #000);
    padding: 1rem 1.5rem;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    z-index: 10000;
    animation: slideIn 0.3s ease-out;
    min-width: 300px;
}

.toast.success {
    background: #10b981;
    color: white;
}

.toast.error {
    background: #ef4444;
    color: white;
}

@keyframes slideIn {
    from {
        transform: translateX(400px);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOut {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(400px);
        opacity: 0;
    }
}
</style>

<script>
// Toast notification system
function showToast(message, type = 'success', duration = 3000) {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toast-message');

    toastMessage.textContent = message;
    toast.className = 'toast ' + type;
    toast.style.display = 'block';

    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => {
            toast.style.display = 'none';
            toast.style.animation = 'slideIn 0.3s ease-out';
        }, 300);
    }, duration);
}
</script>

@endsection
