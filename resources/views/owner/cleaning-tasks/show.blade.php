@extends('layouts.app-neu')

@section('title', 'Schoonmaaktaak Details')

@php
    $portalName = 'Owner Portal';
@endphp

@section('content')
<div class="neu-portal-main">
    <div class="neu-card">
        <div class="card-header-actions">
            <h2 class="card-title">Schoonmaaktaak Details</h2>
        </div>

        <div class="detail-grid">
            <div class="detail-item">
                <span class="detail-label">Kamer</span>
                <span class="detail-value">{{ $cleaningTask->room->room_number }}</span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Datum</span>
                <span class="detail-value">{{ $cleaningTask->date->format('d-m-Y') }}</span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Deadline</span>
                <span class="detail-value">{{ $cleaningTask->deadline->format('d-m-Y H:i') }}</span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Geplande starttijd</span>
                <span class="detail-value">
                    @if($cleaningTask->suggested_start_time)
                        {{ $cleaningTask->suggested_start_time->format('H:i') }}
                    @else
                        <span class="text-muted">Niet gepland</span>
                    @endif
                </span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Geplande duur</span>
                <span class="detail-value">{{ $cleaningTask->planned_duration }} minuten</span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Schoonmaker</span>
                <span class="detail-value">
                    @if($cleaningTask->cleaner)
                        {{ $cleaningTask->cleaner->user->name }}
                    @else
                        <span class="text-muted">Niet toegewezen</span>
                    @endif
                </span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Status</span>
                <span class="detail-value">
                    <span class="badge badge-{{ $cleaningTask->status === 'completed' ? 'success' : ($cleaningTask->status === 'in_progress' ? 'info' : 'warning') }}">
                        {{ ucfirst(str_replace('_', ' ', $cleaningTask->status)) }}
                    </span>
                </span>
            </div>

            @if($cleaningTask->actual_start_time)
            <div class="detail-item">
                <span class="detail-label">Daadwerkelijke starttijd</span>
                <span class="detail-value">{{ $cleaningTask->actual_start_time->format('d-m-Y H:i') }}</span>
            </div>
            @endif

            @if($cleaningTask->actual_end_time)
            <div class="detail-item">
                <span class="detail-label">Daadwerkelijke eindtijd</span>
                <span class="detail-value">{{ $cleaningTask->actual_end_time->format('d-m-Y H:i') }}</span>
            </div>
            @endif

            @if($cleaningTask->actual_duration)
            <div class="detail-item">
                <span class="detail-label">Daadwerkelijke duur</span>
                <span class="detail-value">{{ $cleaningTask->actual_duration }} minuten</span>
            </div>
            @endif

            <div class="detail-item">
                <span class="detail-label">Boeking</span>
                <span class="detail-value">
                    Check-in: {{ $cleaningTask->booking->check_in_datetime->format('d-m-Y H:i') }}
                </span>
            </div>

            @if($cleaningTask->booking->notes)
            <div class="detail-item full-width">
                <span class="detail-label">Boeking notities</span>
                <span class="detail-value">{{ $cleaningTask->booking->notes }}</span>
            </div>
            @endif
        </div>

        @if($cleaningTask->taskLogs->count() > 0)
        <div class="mt-6">
            <h3 class="text-lg font-semibold mb-4">Taak Logboek</h3>
            <div class="table-container">
                <table class="neu-table">
                    <thead>
                        <tr>
                            <th>Actie</th>
                            <th>Tijd</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cleaningTask->taskLogs as $log)
                        <tr>
                            <td>{{ ucfirst($log->action) }}</td>
                            <td>{{ $log->timestamp->format('d-m-Y H:i:s') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
