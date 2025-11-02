<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Falter Verwalter')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-base-100">
    <div class="drawer drawer-mobile">
        <input id="sidebar-toggle" type="checkbox" class="drawer-toggle" />

        <!-- Page Content -->
        <div class="drawer-content flex flex-col">
            <!-- Navbar -->
            <div class="navbar bg-primary text-primary-content sticky top-0 z-40">
                <div class="flex-1">
                    <label for="sidebar-toggle" class="btn btn-ghost drawer-button lg:hidden">
                        ☰
                    </label>
                    <h1 class="text-2xl font-bold ml-4">🦋 Falter Verwalter</h1>
                </div>
                <div class="flex items-center gap-4">
                    @auth
                        <span class="text-sm">{{ Auth::user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="btn btn-ghost btn-sm">Logout</button>
                        </form>
                    @endauth
                </div>
            </div>

            <!-- Main Content -->
            <div class="flex-1 p-4 lg:p-8">
                @yield('content')
            </div>
        </div>

        <!-- Sidebar -->
        <div class="drawer-side">
            <label for="sidebar-toggle" class="drawer-overlay"></label>
            <ul class="menu p-4 w-80 bg-base-200 text-base-content space-y-2">
                <li class="menu-title">
                    <span>Verwaltung</span>
                </li>

                <li>
                    <a href="{{ route('admin.species.index') }}" @class(['active' => request()->routeIs('admin.species.*')])>
                        🦋 Schmetterlingsarten
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.families.index') }}" @class(['active' => request()->routeIs('admin.families.*')])>
                        👨‍👩‍👧 Familien
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.habitats.index') }}" @class(['active' => request()->routeIs('admin.habitats.*')])>
                        🌲 Lebensräume
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.plants.index') }}" @class(['active' => request()->routeIs('admin.plants.*')])>
                        🌱 Pflanzen
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.life-forms.index') }}" @class(['active' => request()->routeIs('admin.life-forms.*')])>
                        🌿 Lebensarten
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.distribution-areas.index') }}" @class(['active' => request()->routeIs('admin.distribution-areas.*')])>
                        🗺️ Verbreitungsgebiete
                    </a>
                </li>
            </ul>
        </div>
    </div>

    @livewireScripts
</body>
</html>
