@extends('layouts.public')

@section('title', 'Schmetterlinge durchsuchen - Falter Verwalter')

@section('content')
    <h1 class="text-4xl font-bold mb-8">🦋 Schmetterlinge durchsuchen</h1>

    <!-- Species Browser Component will be loaded here -->
    @livewire('Public\\SpeciesBrowser')
@endsection
