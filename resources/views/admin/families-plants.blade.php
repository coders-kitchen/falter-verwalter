@extends('layouts.app')

@section('title', 'Familien (Pflanzen)')

@section('content')
    @livewire('FamilyManager', ['type' => 'plant'])
@endsection
