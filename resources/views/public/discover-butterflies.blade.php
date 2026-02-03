@extends('layouts.public')

@section('title', 'Schmetterlinge nach Pflanzen entdecken - Falter Verwalter')

@section('content')
    <h1 class="text-4xl font-bold mb-8">🌱 Schmetterlinge nach Gartenpflanzen entdecken</h1>

    <!-- Plant Butterfly Finder Component will be loaded here -->
    @livewire('Public\\PlantButterflyFinder')
@endsection
