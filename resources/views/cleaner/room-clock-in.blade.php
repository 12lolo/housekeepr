@extends('layouts.app-neu')

@section('title', 'Kamer ' . $room_number)

@php
    $portalName = 'Schoonmaker';
@endphp

@section('page-title', 'Kamer ' . $room_number)

@section('content')
<div style="max-width: 600px; margin: 0 auto; padding: 1rem;">

    @if($error === 'no_task')
        <!-- No Task Found -->
        <div class="neu-alert danger" style="margin-bottom: 2rem;">
            <div class="alert-icon">
                <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="alert-content">
                <div class="alert-title">Geen taak gevonden</div>
                <div class="alert-message">
                    Je hebt vandaag geen schoonmaaktaak voor kamer {{ $room_number }}.
                </div>
            </div>
        </div>

        <a href="{{ route('cleaner.dashboard') }}" class="neu-button-primary" style="width: 100%; text-align: center; display: block; padding: 1rem;">
            Ga naar Dashboard
        </a>

    @elseif($error === 'already_completed')
        <!-- Task Already Completed -->
        <div class="neu-alert success" style="margin-bottom: 2rem;">
            <div class="alert-icon">
                <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="alert-content">
                <div class="alert-title">Taak al voltooid</div>
                <div class="alert-message">
                    Deze kamer is al schoongemaakt om {{ $task->actual_end_time->format('H:i') }}.
                </div>
            </div>
        </div>

        <a href="{{ route('cleaner.dashboard') }}" class="neu-button-primary" style="width: 100%; text-align: center; display: block; padding: 1rem;">
            Ga naar Dashboard
        </a>

    @elseif($error === 'already_started')
        <!-- Task Already Started -->
        <div class="neu-widget" style="margin-bottom: 2rem;">
            <div class="widget-body" style="padding: 1.5rem;">
                <div style="text-align: center; margin-bottom: 1.5rem;">
                    <div style="font-size: 3rem; margin-bottom: 0.5rem;">⏱️</div>
                    <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--neu-text-primary); margin-bottom: 0.5rem;">
                        Al bezig
                    </h3>
                    <p style="color: var(--neu-text-secondary);">
                        Je bent deze taak al gestart om {{ $task->actual_start_time->format('H:i') }}
                    </p>
                </div>

                <div style="padding: 1rem; background: var(--neu-bg-light); border-radius: 12px; margin-bottom: 1.5rem;">
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                        <div style="text-align: center;">
                            <div style="font-size: 0.75rem; color: var(--neu-text-secondary); margin-bottom: 0.25rem; text-transform: uppercase;">Gestart</div>
                            <div style="font-weight: 600; color: var(--neu-text-primary);">
                                {{ $task->actual_start_time->format('H:i') }}
                            </div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 0.75rem; color: var(--neu-text-secondary); margin-bottom: 0.25rem; text-transform: uppercase;">Deadline</div>
                            <div style="font-weight: 600; color: var(--neu-text-primary);">
                                {{ $task->deadline->format('H:i') }}
                            </div>
                        </div>
                    </div>
                </div>

                <a href="{{ route('cleaner.dashboard') }}" class="neu-button-primary" style="width: 100%; text-align: center; display: block; padding: 1rem;">
                    Ga naar Dashboard
                </a>
            </div>
        </div>

    @else
        <!-- Ready to Clock In -->
        <div class="neu-widget" style="margin-bottom: 2rem;">
            <div class="widget-body" style="padding: 1.5rem;">
                <div style="text-align: center; margin-bottom: 1.5rem;">
                    <div style="font-size: 4rem; margin-bottom: 0.5rem;">🧹</div>
                    <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--neu-text-primary); margin-bottom: 0.5rem;">
                        Kamer {{ $room_number }}
                    </h3>
                    @if($task->booking)
                        <p style="color: var(--neu-text-secondary); font-size: 1rem;">
                            Gast: {{ $task->booking->guest_name }}
                        </p>
                    @endif
                </div>

                <!-- Task Details -->
                <div style="padding: 1rem; background: var(--neu-bg-light); border-radius: 12px; margin-bottom: 1.5rem;">
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                        <div style="text-align: center;">
                            <div style="font-size: 0.75rem; color: var(--neu-text-secondary); margin-bottom: 0.25rem; text-transform: uppercase;">Start</div>
                            <div style="font-weight: 600; color: var(--neu-text-primary);">
                                {{ $task->suggested_start_time ? $task->suggested_start_time->format('H:i') : '-' }}
                            </div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 0.75rem; color: var(--neu-text-secondary); margin-bottom: 0.25rem; text-transform: uppercase;">Deadline</div>
                            <div style="font-weight: 600; color: var(--neu-text-primary);">
                                {{ $task->deadline->format('H:i') }}
                            </div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 0.75rem; color: var(--neu-text-secondary); margin-bottom: 0.25rem; text-transform: uppercase;">Duur</div>
                            <div style="font-weight: 600; color: var(--neu-text-primary);">
                                {{ $task->planned_duration }}m
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Clock In Button -->
                <form action="{{ route('cleaner.tasks.start', $task) }}" method="POST" style="margin-bottom: 1rem;">
                    @csrf
                    <button type="submit" class="neu-button-primary" style="width: 100%; padding: 1.25rem; font-size: 1.125rem; font-weight: 700;">
                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20" style="display: inline-block; vertical-align: middle; margin-right: 0.5rem;">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                        </svg>
                        Klok In - Start Schoonmaken
                    </button>
                </form>

                <a href="{{ route('cleaner.dashboard') }}" style="display: block; text-align: center; color: var(--neu-text-secondary); text-decoration: none; padding: 0.5rem;">
                    Terug naar dashboard
                </a>
            </div>
        </div>
    @endif

</div>
@endsection
