@extends('layouts.app')

@section('title', 'Triben verwalten')

@section('content')
<livewire:tribe-manager :subfamilyId="$subfamilyId" />
@endsection
