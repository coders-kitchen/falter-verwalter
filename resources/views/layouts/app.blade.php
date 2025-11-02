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
            <div class="p-4 w-80 bg-base-200 text-base-content min-h-screen flex flex-col">
                <!-- Sidebar Header -->
                <h2 class="text-xl font-bold mb-6">🦋 Verwaltung</h2>

                <!-- Navigation Menu -->
                <ul class="menu space-y-2 flex-1">
                    <!-- Schmetterlinge Section -->
                    <li>
                        <details open class="group">
                            <summary class="cursor-pointer font-semibold text-base flex items-center gap-2">
                                <span>🦋 Schmetterlinge</span>
                                <span class="ml-auto transition-transform group-open:rotate-180">▼</span>
                            </summary>
                            <ul class="pl-4 space-y-1 mt-2">
                                <li>
                                    <a href="{{ route('admin.species.index') }}" @class(['active' => request()->routeIs('admin.species.*')])>
                                        Arten verwalten
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.families.index') }}" @class(['active' => request()->routeIs('admin.families.*')])>
                                        Familien verwalten
                                    </a>
                                </li>
                            </ul>
                        </details>
                    </li>

                    <!-- Lebensraum Section -->
                    <li>
                        <details class="group">
                            <summary class="cursor-pointer font-semibold text-base flex items-center gap-2">
                                <span>🌲 Lebensraum</span>
                                <span class="ml-auto transition-transform group-open:rotate-180">▼</span>
                            </summary>
                            <ul class="pl-4 space-y-1 mt-2">
                                <li>
                                    <a href="{{ route('admin.habitats.index') }}" @class(['active' => request()->routeIs('admin.habitats.*')])>
                                        Lebensräume verwalten
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.plants.index') }}" @class(['active' => request()->routeIs('admin.plants.*')])>
                                        Pflanzen verwalten
                                    </a>
                                </li>
                            </ul>
                        </details>
                    </li>

                    <!-- Klassifikation Section -->
                    <li>
                        <details class="group">
                            <summary class="cursor-pointer font-semibold text-base flex items-center gap-2">
                                <span>📚 Klassifikation</span>
                                <span class="ml-auto transition-transform group-open:rotate-180">▼</span>
                            </summary>
                            <ul class="pl-4 space-y-1 mt-2">
                                <li>
                                    <a href="{{ route('admin.life-forms.index') }}" @class(['active' => request()->routeIs('admin.life-forms.*')])>
                                        Lebensarten verwalten
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.distribution-areas.index') }}" @class(['active' => request()->routeIs('admin.distribution-areas.*')])>
                                        Verbreitungsgebiete verwalten
                                    </a>
                                </li>
                            </ul>
                        </details>
                    </li>
                </ul>

                <!-- Footer Info -->
                <div class="divider my-4"></div>
                <div class="text-xs text-base-content/60">
                    <p>Logged in as:</p>
                    <p class="font-semibold">{{ Auth::user()->name ?? 'User' }}</p>
                </div>
            </div>
        </div>
    </div>

    @livewireScripts
</body>
</html>
