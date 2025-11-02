@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="space-y-6">
        <!-- Welcome Header -->
        <div class="card bg-primary text-primary-content shadow-xl">
            <div class="card-body">
                <h1 class="card-title text-4xl mb-2">🦋 Willkommen bei Falter Verwalter!</h1>
                <p class="text-lg">Admin Panel zur Verwaltung von Schmetterlingsdaten</p>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Species Count -->
            <div class="card bg-base-200 shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-2xl">🦋</h2>
                    <p class="text-sm text-gray-600">Schmetterlingsarten</p>
                    <div class="text-3xl font-bold">{{ \App\Models\Species::count() }}</div>
                    <div class="card-actions justify-end mt-4">
                        <a href="{{ route('admin.species.index') }}" class="btn btn-sm btn-primary">Verwalten</a>
                    </div>
                </div>
            </div>

            <!-- Families Count -->
            <div class="card bg-base-200 shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-2xl">👨‍👩‍👧</h2>
                    <p class="text-sm text-gray-600">Familien</p>
                    <div class="text-3xl font-bold">{{ \App\Models\Family::count() }}</div>
                    <div class="card-actions justify-end mt-4">
                        <a href="{{ route('admin.families.index') }}" class="btn btn-sm btn-primary">Verwalten</a>
                    </div>
                </div>
            </div>

            <!-- Habitats Count -->
            <div class="card bg-base-200 shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-2xl">🌲</h2>
                    <p class="text-sm text-gray-600">Lebensräume</p>
                    <div class="text-3xl font-bold">{{ \App\Models\Habitat::count() }}</div>
                    <div class="card-actions justify-end mt-4">
                        <a href="{{ route('admin.habitats.index') }}" class="btn btn-sm btn-primary">Verwalten</a>
                    </div>
                </div>
            </div>

            <!-- Plants Count -->
            <div class="card bg-base-200 shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-2xl">🌿</h2>
                    <p class="text-sm text-gray-600">Pflanzen</p>
                    <div class="text-3xl font-bold">{{ \App\Models\Plant::count() }}</div>
                    <div class="card-actions justify-end mt-4">
                        <a href="{{ route('admin.plants.index') }}" class="btn btn-sm btn-primary">Verwalten</a>
                    </div>
                </div>
            </div>

            <!-- Life Forms Count -->
            <div class="card bg-base-200 shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-2xl">🌱</h2>
                    <p class="text-sm text-gray-600">Lebensarten</p>
                    <div class="text-3xl font-bold">{{ \App\Models\LifeForm::count() }}</div>
                    <div class="card-actions justify-end mt-4">
                        <a href="{{ route('admin.life-forms.index') }}" class="btn btn-sm btn-primary">Verwalten</a>
                    </div>
                </div>
            </div>

            <!-- Distribution Areas Count -->
            <div class="card bg-base-200 shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-2xl">🗺️</h2>
                    <p class="text-sm text-gray-600">Verbreitungsgebiete</p>
                    <div class="text-3xl font-bold">{{ \App\Models\DistributionArea::count() }}</div>
                    <div class="card-actions justify-end mt-4">
                        <a href="{{ route('admin.distribution-areas.index') }}" class="btn btn-sm btn-primary">Verwalten</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Getting Started -->
            <div class="card bg-base-200 shadow-lg">
                <div class="card-body">
                    <h2 class="card-title">📚 Erste Schritte</h2>
                    <p class="text-sm mb-4">
                        Verwenden Sie das Navigationsmenü auf der linken Seite um die verschiedenen Datentypen zu verwalten.
                        Jede Verwaltungsseite unterstützt Suche, Filterung und Pagination.
                    </p>
                    <div class="card-actions">
                        <a href="{{ route('admin.species.index') }}" class="link link-primary">Zu Schmetterlingsarten →</a>
                    </div>
                </div>
            </div>

            <!-- Features -->
            <div class="card bg-base-200 shadow-lg">
                <div class="card-body">
                    <h2 class="card-title">✨ Features</h2>
                    <ul class="list list-disc list-inside text-sm space-y-2">
                        <li>Echtzeitsuche und Filterung</li>
                        <li>Hierarchische Lebensräume</li>
                        <li>Komplexe Pflanzeneigenschaften</li>
                        <li>Beziehungsverwaltung</li>
                        <li>Deutsche Benutzeroberfläche</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
