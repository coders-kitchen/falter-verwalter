@extends('layouts.app')

@section('title', 'Verbreitungsgebiete verwalten')

@section('content')
<livewire:species-distribution-area-manager :speciesId="$speciesId" />
@endsection
