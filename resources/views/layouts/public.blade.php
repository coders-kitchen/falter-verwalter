<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Falter Verwalter - Schmetterlinge entdecken')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-base-100">
    <!-- Navigation Bar -->
    <nav class="navbar bg-primary text-primary-content sticky top-0 z-40">
        <div class="flex-1">
            <a href="{{ route('home') }}" class="btn btn-ghost normal-case text-xl hover:bg-primary-focus transition">
                🦋 Falter Verwalter
            </a>
        </div>
        <div class="flex-none gap-4">
            <a href="{{ route('species.index') }}" class="btn btn-ghost btn-sm">
                🦋 Schmetterlinge
            </a>
            <a href="{{ route('discover.index') }}" class="btn btn-ghost btn-sm">
                🌱 Pflanzen
            </a>
            <a href="{{ route('map.index') }}" class="btn btn-ghost btn-sm">
                📍 Karte
            </a>
        </div>
    </nav>

    <!-- Breadcrumbs (optional, can be overridden) -->
    @if (isset($breadcrumbs) && count($breadcrumbs) > 0)
        <div class="px-4 py-3 bg-base-200">
            <div class="max-w-6xl mx-auto">
                <div class="text-sm breadcrumbs">
                    <ul>
                        <li><a href="{{ route('home') }}">🏠 Home</a></li>
                        @foreach ($breadcrumbs as $label => $url)
                            @if ($loop->last)
                                <li class="text-base-content/70">{{ $label }}</li>
                            @else
                                <li><a href="{{ $url }}">{{ $label }}</a></li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main class="flex-grow">
        <div class="max-w-6xl mx-auto px-4 py-8">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer footer-center p-10 bg-base-200 text-base-content mt-16">
        <div class="grid grid-cols-1 gap-4">
            <div>
                <h3 class="font-bold text-lg">🦋 Falter Verwalter</h3>
                <p class="text-sm opacity-75">
                    Entdecken Sie die faszinierende Welt der Schmetterlinge und ihrer Lebensräume.
                </p>
            </div>
            <div class="text-sm opacity-75">
                <p>&copy; {{ date('Y') }} Falter Verwalter. Alle Rechte vorbehalten.</p>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
