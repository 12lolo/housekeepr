@extends('layouts.app-neu')

@php
    $portalName = 'Cleaner';
@endphp

@section('page-title', 'Probleem Details')

@section('nav')
    <a href="{{ route('cleaner.dashboard') }}">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
        </svg>
        Dashboard
    </a>
@endsection

@section('content')
    <div class="neu-widget">
        <div class="widget-header">
            <h2>Probleem Details</h2>
            <a href="{{ route('cleaner.dashboard') }}" class="neu-button-secondary">Terug naar Dashboard</a>
        </div>
        <div class="widget-body">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                <div>
                    <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">Kamer</div>
                    <div style="font-weight: 600; font-size: 1.25rem;">{{ $issue->room->room_number }}</div>
                    @if($issue->room->room_type)
                        <div style="font-size: 0.875rem; color: var(--text-muted);">{{ $issue->room->room_type }}</div>
                    @endif
                </div>
                <div>
                    <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">Impact</div>
                    <div>
                        @if($issue->impact === 'kan_niet_gebruikt')
                            <span class="neu-badge danger">Kamer Geblokkeerd</span>
                        @elseif($issue->impact === 'graag_snel')
                            <span class="neu-badge warning">Graag Snel</span>
                        @else
                            <span class="neu-badge">Geen Haast</span>
                        @endif
                    </div>
                </div>
                <div>
                    <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">Status</div>
                    <div>
                        @if($issue->status === 'open')
                            <span class="neu-badge danger">Open</span>
                        @else
                            <span class="neu-badge success">Gefixt</span>
                        @endif
                    </div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                <div>
                    <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">Gemeld Door</div>
                    <div style="font-weight: 600;">{{ $issue->reportedBy->name }}</div>
                </div>
                <div>
                    <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">Gemeld Op</div>
                    <div style="font-weight: 600;">{{ $issue->created_at->format('d-m-Y H:i') }}</div>
                </div>
            </div>

            <div style="margin-top: 1.5rem;">
                <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.5rem;">Beschrijving</div>
                <div style="padding: 1.5rem; background: var(--bg-secondary); border-radius: 12px; white-space: pre-wrap;">{{ $issue->note }}</div>
            </div>

            @if($issue->photo_path)
            <div style="margin-top: 1.5rem;">
                <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.5rem;">Foto</div>
                <div style="padding: 1rem; background: var(--bg-secondary); border-radius: 12px;">
                    <img src="{{ asset('storage/' . $issue->photo_path) }}" alt="Issue Photo" style="max-width: 100%; height: auto; border-radius: 8px;">
                </div>
            </div>
            @endif

            @if($issue->status === 'gefixt')
            <div style="margin-top: 1.5rem; padding: 1rem; background: var(--success); border-radius: 12px; color: white;">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20" style="display: inline-block; vertical-align: middle; margin-right: 0.5rem;">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                Dit probleem is gemarkeerd als opgelost door de eigenaar.
            </div>
            @endif
        </div>
    </div>
@endsection
