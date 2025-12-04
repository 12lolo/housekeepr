@extends('layouts.app-neu')

@php
    $portalName = 'Cleaner';
@endphp

@section('page-title', 'Probleem Melden')

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
            <h2>Probleem Melden</h2>
            <a href="{{ route('cleaner.dashboard') }}" class="neu-button-secondary">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" style="display: inline-block; vertical-align: middle; margin-right: 0.5rem;">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                </svg>
                Terug
            </a>
        </div>
        <div class="widget-body">
            <form action="{{ route('cleaner.issues.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                @if($task)
                    <input type="hidden" name="task_id" value="{{ $task->id }}">

                    <div style="background: var(--bg-secondary); padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem;">
                        <div style="font-weight: 600; margin-bottom: 0.5rem;">Kamer</div>
                        <div style="font-size: 1.25rem;">{{ $task->room->room_number }}</div>
                        @if($task->room->room_type)
                            <div style="font-size: 0.875rem; color: var(--text-muted);">{{ $task->room->room_type }}</div>
                        @endif
                    </div>
                @else
                    <div class="neu-form-group">
                        <label for="task_id" class="neu-label">Taak / Kamer</label>
                        <select
                            id="task_id"
                            name="task_id"
                            class="neu-input @error('task_id') error @enderror"
                            required
                        >
                            <option value="">Selecteer een taak</option>
                            @foreach(auth()->user()->cleaner->cleaningTasks()->where('date', '>=', today()->subDays(7))->with('room')->get() as $cleaningTask)
                                <option value="{{ $cleaningTask->id }}">
                                    {{ $cleaningTask->room->room_number }} - {{ $cleaningTask->date->format('d-m-Y') }}
                                </option>
                            @endforeach
                        </select>
                        @error('task_id')
                            <span class="neu-error">{{ $message }}</span>
                        @enderror
                    </div>
                @endif

                <div class="neu-form-group">
                    <label for="impact" class="neu-label">Impact</label>
                    <select
                        id="impact"
                        name="impact"
                        class="neu-input @error('impact') error @enderror"
                        required
                    >
                        <option value="">Selecteer impact niveau</option>
                        <option value="geen_haast">Geen Haast</option>
                        <option value="graag_snel">Graag Snel</option>
                        <option value="kan_niet_gebruikt">Kamer Kan Niet Gebruikt Worden (URGENT!)</option>
                    </select>
                    @error('impact')
                        <span class="neu-error">{{ $message }}</span>
                    @enderror
                    <small class="neu-hint">Let op: "Kamer Kan Niet Gebruikt Worden" stuurt direct een email naar de eigenaar.</small>
                </div>

                <div class="neu-form-group">
                    <label for="note" class="neu-label">Beschrijving Probleem</label>
                    <textarea
                        id="note"
                        name="note"
                        class="neu-input @error('note') error @enderror"
                        rows="5"
                        placeholder="Beschrijf wat er aan de hand is..."
                        required
                    >{{ old('note') }}</textarea>
                    @error('note')
                        <span class="neu-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="neu-form-group">
                    <label for="photo" class="neu-label">Foto (optioneel)</label>
                    <input
                        type="file"
                        id="photo"
                        name="photo"
                        class="neu-input @error('photo') error @enderror"
                        accept="image/jpeg,image/png,image/jpg"
                    >
                    @error('photo')
                        <span class="neu-error">{{ $message }}</span>
                    @enderror
                    <small class="neu-hint">Max 5MB. Ondersteunde formaten: JPG, JPEG, PNG</small>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="neu-button-primary">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" style="display: inline-block; vertical-align: middle; margin-right: 0.5rem;">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        Probleem Melden
                    </button>
                    <a href="{{ route('cleaner.dashboard') }}" class="neu-button-secondary">Annuleren</a>
                </div>
            </form>
        </div>
    </div>
@endsection
