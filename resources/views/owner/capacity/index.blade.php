@extends('layouts.app-neu')

@php
    $portalName = 'Owner';
@endphp

@section('page-title', 'Capaciteit Beheer')

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
    <a href="{{ route('owner.capacity.index') }}" class="active">
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
            <h2>Dagelijkse Capaciteit Instellen</h2>
        </div>
        <div class="widget-body">
            <div class="capacity-info-box">
                <div class="info-box-title">Info</div>
                <div class="info-box-text">
                    Je hebt momenteel <strong>{{ $totalCleaners }}</strong> actieve schoonmaker(s).
                    Stel de dagelijkse capaciteit in om de planner te helpen taken correct te verdelen.
                </div>
            </div>

            <!-- Bulk Capacity Form -->
            <div class="neu-widget" class="mb-6">
                <div class="p-6">
                    <h3 class="mb-4">Bulkinstelling (meerdere dagen)</h3>
                    <form action="{{ route('owner.capacity.bulk-store') }}" method="POST">
                        @csrf
                        <div class="capacity-grid">
                            <div class="neu-form-group" class="mb-0">
                                <label for="start_date" class="neu-label">Van</label>
                                <input
                                    type="date"
                                    id="start_date"
                                    name="start_date"
                                    class="neu-input"
                                    min="{{ today()->toDateString() }}"
                                    value="{{ today()->toDateString() }}"
                                    required
                                >
                            </div>
                            <div class="neu-form-group" class="mb-0">
                                <label for="end_date" class="neu-label">Tot</label>
                                <input
                                    type="date"
                                    id="end_date"
                                    name="end_date"
                                    class="neu-input"
                                    min="{{ today()->toDateString() }}"
                                    required
                                >
                            </div>
                            <div class="neu-form-group" class="mb-0">
                                <label for="bulk_capacity" class="neu-label">Capaciteit</label>
                                <input
                                    type="number"
                                    id="bulk_capacity"
                                    name="capacity"
                                    class="neu-input"
                                    min="0"
                                    max="50"
                                    required
                                >
                            </div>
                            <div class="d-flex align-items-end">
                                <button type="submit" class="neu-button-primary" class="w-full">Instellen</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Individual Days Grid -->
            <h3 class="mb-4">Komende 30 Dagen</h3>
            <div class="days-grid">
                @for($i = 0; $i < 30; $i++)
                    @php
                        $date = today()->addDays($i);
                        $dateStr = $date->toDateString();
                        $existingCapacity = $capacities->get($dateStr);
                    @endphp

                    <div class="neu-widget" class="p-4">
                        <div class="info-box-title">
                            {{ $date->format('D d M') }}
                        </div>

                        <form action="{{ route('owner.capacity.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="date" value="{{ $dateStr }}">

                            <div style="display: flex; gap: 0.5rem; align-items: center;">
                                <input
                                    type="number"
                                    name="capacity"
                                    class="neu-input"
                                    style="flex: 1; margin: 0;"
                                    min="0"
                                    max="50"
                                    value="{{ $existingCapacity?->capacity ?? '' }}"
                                    placeholder="0"
                                    required
                                >
                                <button type="submit" class="neu-button-primary" style="padding: 0.5rem 1rem;">
                                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </div>
                        </form>

                        @if($existingCapacity)
                        <form action="{{ route('owner.capacity.destroy', $existingCapacity) }}" method="POST" style="margin-top: 0.5rem;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="neu-button-secondary" style="width: 100%; font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                Reset
                            </button>
                        </form>
                        @endif
                    </div>
                @endfor
            </div>
        </div>
    </div>
@endsection
