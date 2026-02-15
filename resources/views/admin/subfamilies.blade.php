@extends('layouts.app')

@section('title', 'Unterfamilien verwalten')

@section('content')
<livewire:subfamily-manager :familyId="$familyId" />
@endsection
