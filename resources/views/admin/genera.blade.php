@extends('layouts.app')

@section('title', 'Gattungen verwalten')

@section('content')
<livewire:genus-manager :subfamilyId="$subfamilyId" />
@endsection
