@extends('layouts.app')

@section('title', 'Familien (Schmetterlinge)')

@section('content')
    @livewire('FamilyManager', ['type' => 'butterfly'])
@endsection
