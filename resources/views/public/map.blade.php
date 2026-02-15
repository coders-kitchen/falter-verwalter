@extends('layouts.public')

@section('title', 'Verbreitungsgebiete - Falter Verwalter')

@section('content')
    <h1 class="text-4xl font-bold mb-8">📍 Verbreitungsgebiete der Schmetterlinge</h1>

    <!-- Regional Distribution Map Component will be loaded here -->
    @livewire('Public\\RegionalDistributionMap')
@endsection
