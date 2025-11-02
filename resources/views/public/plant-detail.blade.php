@extends('layouts.public')

@section('title', (isset($plant) ? $plant->name : 'Pflanze') . ' - Falter Verwalter')

@section('content')
    @if (isset($plant))
        <div class="mb-6">
            <a href="{{ route('discover.index') }}" class="btn btn-sm btn-ghost">
                ← Zurück zur Suche
            </a>
        </div>

        <h1 class="text-4xl font-bold mb-4">🌿 {{ $plant->name }}</h1>

        <div class="alert alert-info mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div>
                <h3 class="font-bold">Seite wird geladen</h3>
                <div class="text-sm">Die Details für {{ $plant->name }} werden in Kürze angezeigt.</div>
            </div>
        </div>

        <!-- Plant Detail Component will be loaded here -->
        @livewire('Public\\PlantDetail', ['plant' => $plant])
    @else
        <div class="alert alert-error">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l-2-2m0 0l-2-2m2 2l2-2m-2 2l-2 2"></path></svg>
            <div>
                <h3 class="font-bold">Pflanze nicht gefunden</h3>
                <div class="text-sm">
                    Die angeforderte Pflanze konnte nicht gefunden werden.
                    <a href="{{ route('discover.index') }}" class="link">Zurück zur Suche</a>
                </div>
            </div>
        </div>
    @endif
@endsection
