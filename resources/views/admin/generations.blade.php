@extends('layouts.app')

@section('title', 'Generationen verwalten')

@section('content')
<livewire:generation-manager :speciesId="$speciesId" />
@endsection
