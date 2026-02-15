<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Falter Verwalter')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <script>
        // Initialize sidebar collapse state from localStorage
        document.addEventListener('DOMContentLoaded', function() {
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (isCollapsed) {
                document.documentElement.classList.add('sidebar-collapsed');
            }
        });
    </script>
</head>
<body class="bg-base-100">
    <div class="drawer lg:drawer-open">
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
        <div class="drawer-side z-50">
            <label for="sidebar-toggle" class="drawer-overlay lg:hidden"></label>
            <div class="sidebar-container flex flex-col bg-base-200 text-base-content min-h-screen transition-all duration-300 ease-in-out" id="sidebarContainer">
                <!-- Sidebar Header with Collapse Button -->
                <div class="p-4 flex items-center justify-between border-b border-base-300">
                    <h2 class="text-xl font-bold sidebar-text transition-opacity duration-300" style="opacity: 1;">🦋 Verwaltung</h2>
                    <button
                        onclick="toggleSidebar()"
                        class="btn btn-ghost btn-sm hidden lg:flex"
                        title="Sidebar umschalten"
                        aria-label="Sidebar collapse toggle">
                        <span class="sidebar-toggle-icon transition-transform duration-300">◀</span>
                    </button>
                </div>

                <!-- Navigation Menu -->
                <ul class="menu space-y-1 flex-1 p-3">
                    <!-- Schmetterlinge Section -->
                    <li>
                        <details open class="group">
                            <summary class="cursor-pointer font-semibold flex items-center gap-2 py-2 px-3 rounded hover:bg-base-300 transition-colors">
                                <span class="text-lg">🦋</span>
                                <span class="sidebar-text transition-opacity duration-300" style="opacity: 1;">Schmetterlinge</span>
                                <span class="ml-auto sidebar-chevron transition-transform duration-300 group-open:rotate-180">▼</span>
                            </summary>
                            <ul class="sidebar-submenu pl-8 space-y-1 mt-2 transition-all duration-300" style="max-height: 500px; opacity: 1;">
                                <li>
                                    <a href="{{ route('admin.species.index') }}" @class(['active' => request()->routeIs('admin.species.*'), 'block py-1 px-3 rounded hover:bg-base-300 transition-colors']) title="Arten verwalten">
                                        <span class="sidebar-link-text transition-opacity duration-300" style="opacity: 1;">Arten verwalten</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.families.butterflies') }}" @class(['active' => request()->routeIs('admin.families.butterflies') || request()->routeIs('admin.subfamilies.*') || request()->routeIs('admin.tribes.*') || request()->routeIs('admin.genera.*'), 'block py-1 px-3 rounded hover:bg-base-300 transition-colors']) title="Schmetterlingsfamilien verwalten">
                                        <span class="sidebar-link-text transition-opacity duration-300" style="opacity: 1;">Familien (Schmetterlinge)</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.distribution-areas.index') }}" @class(['active' => request()->routeIs('admin.distribution-areas.*'), 'block py-1 px-3 rounded hover:bg-base-300 transition-colors']) title="Verbreitungsgebiete verwalten">
                                        <span class="sidebar-link-text transition-opacity duration-300" style="opacity: 1;">Verbreitungsgebiete verwalten</span>
                                    </a>
                                </li>
                            {{--
                            <li>
                                <a href="#" class="block py-1 px-3 rounded hover:bg-base-300 transition-colors">
                                    <span class="sidebar-link-text transition-opacity duration-300" style="opacity: 1;">Regionen</span>
                                </a>
                            </li>
                            --}}
                            </ul>
                        </details>
                    </li>

                    <!-- Lebensraum Section -->
                    <li>
                        <details class="group">
                            <summary class="cursor-pointer font-semibold flex items-center gap-2 py-2 px-3 rounded hover:bg-base-300 transition-colors">
                                <span class="text-lg">🌲</span>
                                <span class="sidebar-text transition-opacity duration-300" style="opacity: 1;">Lebensraum</span>
                                <span class="ml-auto sidebar-chevron transition-transform duration-300 group-open:rotate-180">▼</span>
                            </summary>
                            <ul class="sidebar-submenu pl-8 space-y-1 mt-2 transition-all duration-300" style="max-height: 500px; opacity: 1;">
                                <li>
                                    <a href="{{ route('admin.habitats.index') }}" @class(['active' => request()->routeIs('admin.habitats.*'), 'block py-1 px-3 rounded hover:bg-base-300 transition-colors']) title="Lebensräume verwalten">
                                        <span class="sidebar-link-text transition-opacity duration-300" style="opacity: 1;">Lebensräume verwalten</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.plants.index') }}" @class(['active' => request()->routeIs('admin.plants.*'), 'block py-1 px-3 rounded hover:bg-base-300 transition-colors']) title="Pflanzen verwalten">
                                        <span class="sidebar-link-text transition-opacity duration-300" style="opacity: 1;">Pflanzen verwalten</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.families.plants') }}" @class(['active' => request()->routeIs('admin.families.plants'), 'block py-1 px-3 rounded hover:bg-base-300 transition-colors']) title="Pflanzenfamilien verwalten">
                                        <span class="sidebar-link-text transition-opacity duration-300" style="opacity: 1;">Familien (Pflanzen)</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.life-forms.index') }}" @class(['active' => request()->routeIs('admin.life-forms.*'), 'block py-1 px-3 rounded hover:bg-base-300 transition-colors']) title="Lebensarten verwalten">
                                        <span class="sidebar-link-text transition-opacity duration-300" style="opacity: 1;">Lebensarten verwalten</span>
                                    </a>
                                </li>
                            </ul>
                        </details>
                    </li>

                    <!-- Klassifikation Section -->
                    <li>
                        <details class="group">
                            <summary class="cursor-pointer font-semibold flex items-center gap-2 py-2 px-3 rounded hover:bg-base-300 transition-colors">
                                <span class="text-lg">📚</span>
                                <span class="sidebar-text transition-opacity duration-300" style="opacity: 1;">Klassifikationen</span>
                                <span class="ml-auto sidebar-chevron transition-transform duration-300 group-open:rotate-180">▼</span>
                            </summary>
                            <ul class="sidebar-submenu pl-8 space-y-1 mt-2 transition-all duration-300" style="max-height: 500px; opacity: 1;">
                                                                <li>
                                    <a href="{{ route('admin.threat-categories.index') }}" @class(['active' => request()->routeIs('admin.threat-categories.*'), 'block py-1 px-3 rounded hover:bg-base-300 transition-colors']) title="Gefährdungsstatus verwalten">
                                        <span class="sidebar-link-text transition-opacity duration-300" style="opacity: 1;">Gefährdungsstatus verwalten</span>
                                    </a>
                                </li>
                            </ul>
                        </details>
                    </li>
                    <li>
                                                <details class="group">
                            <summary class="cursor-pointer font-semibold flex items-center gap-2 py-2 px-3 rounded hover:bg-base-300 transition-colors">
                                <span class="text-lg">📚</span>
                                <span class="sidebar-text transition-opacity duration-300" style="opacity: 1;">Nutzerverwaltung</span>
                                <span class="ml-auto sidebar-chevron transition-transform duration-300 group-open:rotate-180">▼</span>
                            </summary>
                            <ul class="sidebar-submenu pl-8 space-y-1 mt-2 transition-all duration-300" style="max-height: 500px; opacity: 1;">
                                                                <li>
                                    <a href="{{ route('admin.user.index') }}" @class(['active' => request()->routeIs('admin.users.*'), 'block py-1 px-3 rounded hover:bg-base-300 transition-colors']) title="Admins verwalten">
                                        <span class="sidebar-link-text transition-opacity duration-300" style="opacity: 1;">Admins verwalten</span>
                                    </a>
                                </li>
                            </ul>
                        </details>
                    </li>
                </ul>

                <!-- Footer Info -->
                <div class="p-3 border-t border-base-300">
                    <div class="text-xs text-base-content/60 sidebar-footer transition-all duration-300" style="opacity: 1;">
                        <p class="sidebar-footer-label transition-opacity duration-300" style="opacity: 1;">Logged in as:</p>
                        <p class="font-semibold sidebar-footer-name transition-opacity duration-300" style="opacity: 1;">{{ Auth::user()->name ?? 'User' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <style>
            /* Sidebar base width and responsive adjustments */
            html:not(.sidebar-collapsed) .sidebar-container {
                width: 20rem; /* w-80 */
            }

            html.sidebar-collapsed .sidebar-container {
                width: 5rem; /* Collapsed width - icon only */
            }

            /* Hide text elements when sidebar is collapsed */
            html.sidebar-collapsed .sidebar-text,
            html.sidebar-collapsed .sidebar-link-text,
            html.sidebar-collapsed .sidebar-footer-label,
            html.sidebar-collapsed .sidebar-footer-name {
                display: none;
            }

            /* Hide chevron and submenu items when collapsed */
            html.sidebar-collapsed .sidebar-chevron,
            html.sidebar-collapsed .sidebar-submenu {
                display: none;
            }

            /* Center icons when collapsed */
            html.sidebar-collapsed .sidebar-container .menu li > details > summary {
                justify-content: center;
                padding-left: 1.25rem;
            }

            /* Adjust toggle icon */
            html.sidebar-collapsed .sidebar-toggle-icon {
                transform: rotate(180deg);
            }

            /* Smooth height transitions for drawer on mobile */
            @media (max-width: 1023px) {
                html:not(.sidebar-collapsed) .drawer-side {
                    --tw-translate-x: 0;
                }

                html.sidebar-collapsed .drawer-side {
                    --tw-translate-x: -100%;
                }
            }

            /* Ensure drawer content adjusts on desktop */
            @media (min-width: 1024px) {
                html.sidebar-collapsed .drawer-content {
                    margin-left: 0;
                }
            }
        </style>

        <script>
            function toggleSidebar() {
                const html = document.documentElement;
                const isCollapsed = html.classList.contains('sidebar-collapsed');

                if (isCollapsed) {
                    html.classList.remove('sidebar-collapsed');
                    localStorage.setItem('sidebarCollapsed', 'false');
                } else {
                    html.classList.add('sidebar-collapsed');
                    localStorage.setItem('sidebarCollapsed', 'true');
                }
            }
        </script>
    </div>

    @livewireScripts
</body>
</html>
