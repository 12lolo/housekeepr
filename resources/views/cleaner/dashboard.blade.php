@extends('layouts.app-neu')

@section('title', 'Mijn Taken')

@php
    $portalName = 'Schoonmaker';
@endphp

@section('page-title', 'Mijn Taken')

@section('nav')
    <a href="{{ route('cleaner.dashboard') }}" class="active">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
        </svg>
        Mijn Taken
    </a>
@endsection

@section('content')
    <!-- Welcome Message -->
    <div style="margin-bottom: 1.5rem;">
        <p style="color: var(--neu-light-text-muted, #718096); font-size: 0.875rem;">
            Hallo {{ auth()->user()->name }}, hier zijn je taken voor {{ now()->format('d-m-Y') }}.
        </p>
    </div>

    <!-- Mobile-optimized Stats Grid (2x2) -->
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 2rem;">
        <!-- Completed Tasks -->
        <div class="neu-stat-card" style="padding: 1.25rem;">
            <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); width: 44px; height: 44px; box-shadow: 4px 4px 12px rgba(16, 185, 129, 0.3), -4px -4px 12px rgba(255, 255, 255, 0.5);">
                <svg width="20" height="20" fill="white" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="stat-label">Klaar</div>
            <div class="stat-value" style="color: #10b981;">{{ $stats['completed'] }}</div>
        </div>

        <!-- In Progress Tasks -->
        <div class="neu-stat-card" style="padding: 1.25rem;">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); width: 44px; height: 44px; box-shadow: 4px 4px 12px rgba(245, 158, 11, 0.3), -4px -4px 12px rgba(255, 255, 255, 0.5);">
                <svg width="20" height="20" fill="white" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="stat-label">Bezig</div>
            <div class="stat-value" style="color: #f59e0b;">{{ $stats['in_progress'] }}</div>
        </div>

        <!-- Pending Tasks -->
        <div class="neu-stat-card" style="padding: 1.25rem;">
            <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #5568d3 100%); width: 44px; height: 44px; box-shadow: 4px 4px 12px rgba(102, 126, 234, 0.3), -4px -4px 12px rgba(255, 255, 255, 0.5);">
                <svg width="20" height="20" fill="white" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="stat-label">Te doen</div>
            <div class="stat-value" style="color: #667eea;">{{ $stats['pending'] }}</div>
        </div>

        <!-- Total Tasks -->
        <div class="neu-stat-card" style="padding: 1.25rem;">
            <div class="stat-icon" style="width: 44px; height: 44px;">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="stat-label">Totaal</div>
            <div class="stat-value">{{ $stats['total_today'] }}</div>
        </div>
    </div>

    <!-- Task List (Mobile-First) -->
    @if($tasks_today->count() > 0)
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            @foreach($tasks_today as $task)
            <div class="neu-widget" @if($task->status === 'completed') style="opacity: 0.7;" @endif>
                <div class="widget-body" style="padding: 1.25rem;">
                    <!-- Room Header -->
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                        <div>
                            <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--neu-light-text, #4a5568); margin-bottom: 0.25rem;">
                                Kamer {{ $task->room->room_number }}
                            </h3>
                            <span style="font-size: 0.875rem; color: var(--neu-light-text-muted, #718096);">
                                {{ $task->room->room_type ?? 'Standard' }}
                            </span>
                        </div>
                        <div>
                            @if($task->status === 'completed')
                                <span class="neu-badge success" style="white-space: nowrap;">Klaar</span>
                            @elseif($task->status === 'in_progress')
                                <span class="neu-badge warning" style="white-space: nowrap;">Bezig</span>
                            @else
                                <span class="neu-badge" style="white-space: nowrap;">Te doen</span>
                            @endif
                        </div>
                    </div>

                    <!-- Task Details -->
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; margin-bottom: 1rem; padding: 1rem; background: var(--neu-light-bg, #e0e5ec); border-radius: 12px; box-shadow: inset 3px 3px 6px rgba(163, 177, 198, 0.4), inset -3px -3px 6px rgba(255, 255, 255, 0.6);">
                        <div style="text-align: center;">
                            <div style="font-size: 0.75rem; color: var(--neu-light-text-muted, #718096); margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.5px;">Start</div>
                            <div style="font-weight: 600; color: var(--neu-light-text, #4a5568); font-size: 0.875rem;">
                                {{ $task->suggested_start_time ? $task->suggested_start_time->format('H:i') : '-' }}
                            </div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 0.75rem; color: var(--neu-light-text-muted, #718096); margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.5px;">Deadline</div>
                            <div style="font-weight: 600; color: var(--neu-light-text, #4a5568); font-size: 0.875rem;">
                                {{ $task->deadline->format('H:i') }}
                            </div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 0.75rem; color: var(--neu-light-text-muted, #718096); margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.5px;">Duur</div>
                            <div style="font-weight: 600; color: var(--neu-light-text, #4a5568); font-size: 0.875rem;">
                                {{ $task->planned_duration }}m
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons (Large Touch Targets) -->
                    @if($task->status === 'pending')
                        <button class="neu-button-primary" style="width: 100%; padding: 1rem; font-size: 1rem; font-weight: 600;">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20" style="display: inline-block; vertical-align: middle; margin-right: 0.5rem;">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                            </svg>
                            Start Taak
                        </button>
                    @elseif($task->status === 'in_progress')
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                            <button style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); box-shadow: 6px 6px 15px rgba(245, 158, 11, 0.4), -6px -6px 15px rgba(255, 255, 255, 0.6); color: white; border: none; border-radius: 15px; padding: 1rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; font-size: 0.9rem;">
                                <svg width="18" height="18" fill="currentColor" viewBox="0 0 20 20" style="display: block; margin: 0 auto 0.25rem;">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                Pauzeer
                            </button>
                            <button style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 6px 6px 15px rgba(16, 185, 129, 0.4), -6px -6px 15px rgba(255, 255, 255, 0.6); color: white; border: none; border-radius: 15px; padding: 1rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; font-size: 0.9rem;">
                                <svg width="18" height="18" fill="currentColor" viewBox="0 0 20 20" style="display: block; margin: 0 auto 0.25rem;">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Klaar
                            </button>
                        </div>
                        @if($task->actual_start_time)
                        <div style="margin-top: 0.75rem; padding: 0.75rem; background: var(--neu-light-bg, #e0e5ec); border-radius: 10px; box-shadow: inset 2px 2px 5px rgba(163, 177, 198, 0.3), inset -2px -2px 5px rgba(255, 255, 255, 0.5); text-align: center;">
                            <span style="font-size: 0.875rem; color: var(--neu-light-text-muted, #718096);">
                                Gestart om {{ $task->actual_start_time->format('H:i') }}
                            </span>
                        </div>
                        @endif
                    @else
                        <div style="padding: 1rem; background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.1) 100%); border-radius: 12px; text-align: center;">
                            <svg width="20" height="20" fill="#10b981" viewBox="0 0 20 20" style="display: inline-block; vertical-align: middle; margin-right: 0.5rem;">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span style="color: #10b981; font-weight: 600; font-size: 0.875rem;">
                                Voltooid om {{ $task->actual_end_time ? $task->actual_end_time->format('H:i') : '-' }}
                                @if($task->actual_duration)
                                    ({{ $task->actual_duration }} min)
                                @endif
                            </span>
                        </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="neu-empty-state">
            <div class="empty-icon">
                <svg width="48" height="48" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="empty-title">Geen taken voor vandaag</div>
            <div class="empty-message">Geniet van je vrije dag!</div>
        </div>
    @endif

    <!-- Floating Action Button for Issue Reporting (Mobile-Optimized) -->
    <button style="position: fixed; bottom: 2rem; right: 1.5rem; width: 64px; height: 64px; border-radius: 50%; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); box-shadow: 8px 8px 20px rgba(239, 68, 68, 0.4), -8px -8px 20px rgba(255, 255, 255, 0.3), 0 4px 12px rgba(0, 0, 0, 0.15); border: none; cursor: pointer; transition: all 0.3s ease; z-index: 50;"
            onmouseover="this.style.transform='scale(1.05)'"
            onmouseout="this.style.transform='scale(1)'"
            onclick="this.style.boxShadow='inset 4px 4px 10px rgba(220, 38, 38, 0.6), inset -4px -4px 10px rgba(255, 100, 100, 0.3)'">
        <svg width="28" height="28" fill="white" viewBox="0 0 20 20" style="display: block; margin: 0 auto;">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
    </button>

    @media (prefers-color-scheme: dark) {
        <style>
            .neu-stat-card .stat-icon {
                box-shadow: 4px 4px 12px rgba(0, 0, 0, 0.4), -4px -4px 12px rgba(255, 255, 255, 0.05);
            }
        </style>
    }
@endsection
