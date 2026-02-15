@extends('layouts.app')

@section('title', 'Pflanzenzuordnung verwalten')

@section('content')
<livewire:species-plant-manager :speciesId="$speciesId" />
@endsection
