@extends('layouts.public')

@section('title', 'Falter Verwalter - Schmetterlinge und Pflanzen entdecken')

@section('content')
    <!-- Hero Section -->
    <section class="hero min-h-screen bg-gradient-to-r from-blue-100 to-purple-100">
        <div class="hero-content text-center">
            <div class="max-w-2xl">
                <h1 class="text-5xl md:text-6xl font-bold mb-6">
                    🦋 Falter Verwalter
                </h1>
                <p class="text-lg md:text-xl mb-8 text-base-content/80">
                    Entdecken Sie die faszinierende Welt der Schmetterlinge und ihrer Lebensräume.
                    Lernen Sie, welche Schmetterlinge in Ihrer Region vorkommen und welche Pflanzen
                    sie anlocken.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Use Case 1: Species Search -->
                    <a href="{{ route('species.index') }}" class="btn btn-primary btn-lg gap-2 h-auto py-6 flex-col">
                        <span class="text-3xl">🦋</span>
                        <span>
                            <div class="font-bold text-lg">Nach Schmetterlingen suchen</div>
                            <div class="text-sm opacity-90">Finden Sie Arten, Lebensräume und Lebenszyklen</div>
                        </span>
                    </a>

                    <!-- Use Case 2: Plant Discovery -->
                    <a href="{{ route('discover.index') }}" class="btn btn-success btn-lg gap-2 h-auto py-6 flex-col">
                        <span class="text-3xl">🌱</span>
                        <span>
                            <div class="font-bold text-lg">Nach Pflanzen filtern</div>
                            <div class="text-sm opacity-90">Schmetterlinge anlocken mit Ihren Gartenpflanzen</div>
                        </span>
                    </a>
                </div>

                <!-- Secondary CTA -->
                <div class="mt-12 pt-8 border-t border-base-300">
                    <p class="mb-4 text-base-content/70">Oder erkunden Sie unsere interaktive Karte:</p>
                    <a href="{{ route('map.index') }}" class="btn btn-outline btn-lg gap-2">
                        📍 Regionale Verbreitung anzeigen
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 bg-base-200">
        <div class="max-w-6xl mx-auto px-4">
            <h2 class="text-4xl font-bold text-center mb-12">Was können Sie tun?</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h3 class="card-title text-2xl mb-4">
                            <span class="text-3xl">🔍</span> Arten entdecken
                        </h3>
                        <p>
                            Durchsuchen Sie unsere umfangreiche Datenbank von Schmetterlingsarten.
                            Finden Sie detaillierte Informationen zu Lebensräumen, Lebenszyklen und geografischer Verbreitung.
                        </p>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h3 class="card-title text-2xl mb-4">
                            <span class="text-3xl">🌿</span> Schmetterlinge anlocken
                        </h3>
                        <p>
                            Wählen Sie die Pflanzen in Ihrem Garten aus und entdecken Sie,
                            welche Schmetterlinge diese Pflanzen als Nektarquelle oder Futterpflanze nutzen.
                        </p>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h3 class="card-title text-2xl mb-4">
                            <span class="text-3xl">📅</span> Lebenszyklen verstehen
                        </h3>
                        <p>
                            Interaktive Kalender zeigen Flugmonate und Verpuppungsphasen.
                            Verstehen Sie wann Schmetterlinge aktiv sind und wann ihre Futterpflanzen blühen.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Information Section -->
    <section class="py-16 bg-base-100">
        <div class="max-w-4xl mx-auto px-4">
            <h2 class="text-3xl font-bold mb-8">Über diese Datenbank</h2>

            <div class="prose prose-invert max-w-none">
                <p>
                    Falter Verwalter ist eine umfangreiche Datenbank für Schmetterlinge und ihre Lebensräume.
                    Hier finden Sie Informationen über:
                </p>
                <ul class="list-disc list-inside space-y-2">
                    <li><strong>Schmetterlingsarten</strong>: Taxonomie, Lebensräume, geografische Verbreitung</li>
                    <li><strong>Lebenszyklen</strong>: Flugmonate, Verpuppungsphasen, Generationen</li>
                    <li><strong>Pflanzliche Verbindungen</strong>: Nektarpflanzen und Futterpflanzen</li>
                    <li><strong>Gefährdete Regionen</strong>: Wo Arten besonderen Schutz benötigen</li>
                    <li><strong>Lebensräume</strong>: Wo Schmetterlinge natürlicherweise vorkommen</li>
                </ul>
            </div>

            <div class="mt-12 alert alert-info">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div>
                    <h3 class="font-bold">💡 Tipp</h3>
                    <div class="text-sm">
                        Starten Sie mit der <a href="{{ route('species.index') }}" class="link">Artensuchseite</a>
                        oder versuchen Sie direkt, <a href="{{ route('discover.index') }}" class="link">Schmetterlinge anhand Ihrer Gartenpflanzen zu entdecken</a>.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call-to-Action Section -->
    <section class="py-16 bg-gradient-to-r from-primary to-primary-focus text-primary-content">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold mb-6">Bereit zu entdecken?</h2>
            <p class="text-lg mb-8">
                Tauchen Sie in die faszinierende Welt der Schmetterlinge ein.
            </p>
            <div class="flex flex-col md:flex-row gap-4 justify-center">
                <a href="{{ route('species.index') }}" class="btn btn-light btn-lg">
                    🦋 Arten durchsuchen
                </a>
                <a href="{{ route('discover.index') }}" class="btn btn-light btn-outline btn-lg">
                    🌱 Pflanzen entdecken
                </a>
            </div>
        </div>
    </section>
@endsection
