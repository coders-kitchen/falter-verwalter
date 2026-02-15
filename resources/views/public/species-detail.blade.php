@extends('layouts.public')

@section('title', (isset($species) ? $species->name : 'Art') . ' - Falter Verwalter')

@section('content')
    <div>
        @if (isset($species))
            <div class="mb-6">
                <a href="{{ route('species.index') }}" class="btn btn-sm btn-ghost">
                    ← Zurück zur Liste
                </a>
            </div>
            @include('livewire.public.species-detail', ['species' => $species])
        @else
            <div class="alert alert-error">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l-2-2m0 0l-2-2m2 2l2-2m-2 2l-2 2"></path></svg>
                <div>
                    <h3 class="font-bold">Art nicht gefunden</h3>
                    <div class="text-sm">
                        Die angeforderte Art konnte nicht gefunden werden.
                        <a href="{{ route('species.index') }}" class="link">Zurück zur Artenliste</a>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
