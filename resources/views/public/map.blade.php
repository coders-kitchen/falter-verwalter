@extends('layouts.public')

@section('title', 'Regionale Verbreitung - Falter Verwalter')

@section('content')
    <h1 class="text-4xl font-bold mb-8">📍 Regionale Verbreitung der Schmetterlinge</h1>

    <!-- Regional Distribution Map Component will be loaded here -->
    @livewire('Public\\RegionalDistributionMap')
@endsection
