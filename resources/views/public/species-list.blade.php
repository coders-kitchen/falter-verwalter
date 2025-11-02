@extends('layouts.public')

@section('title', 'Schmetterlinge durchsuchen - Falter Verwalter')

@section('content')
    <h1 class="text-4xl font-bold mb-8">🦋 Schmetterlinge durchsuchen</h1>

    <div class="alert alert-info mb-6">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <div>
            <h3 class="font-bold">Seite wird geladen</h3>
            <div class="text-sm">Der Schmetterlinge-Browser wird in Kürze hier angezeigt.</div>
        </div>
    </div>

    <!-- Species Browser Component will be loaded here -->
    @livewire('Public\\SpeciesBrowser')
@endsection
