<div class="space-y-8">
    <!-- Header Section -->
    <div class="space-y-4">
        <h1 class="text-5xl font-bold">🦋 {{ $species->name }}</h1>
        <p class="text-lg opacity-75">{{ $species->code }}</p>

        @if ($species->description)
            <p class="text-base leading-relaxed max-w-3xl">{{ $species->description }}</p>
        @endif
        <!-- All Regions Badges -->
        @if ($species->distributionAreas->count() > 0)
            <div class="flex flex-wrap gap-2 mt-4">
                @foreach ($species->distributionAreas as $distArea)
                    @php
                        $status = $distArea->pivot->threatCategory;
                        $badgeClass = $status->code === 'VU' ? 'badge-error' : 'badge-success';
                        $icon = $status->code === 'VU' ? '⚠️' : '✓';
                    @endphp
                    
                    <span class="badge badge-info badge-lg" style="background: {{ $status->color_code }};">
                        {{ $distArea->name }} - {{ $status->code }} ({{ $status->label }})
                    </span>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Tabs/Sections -->
    <div class="tabs tabs-lifted" role="tablist">
        <!-- Tab 1: Taxonomy -->
        <input
            type="radio"
            name="species_tabs"
            role="tab"
            class="tab"
            aria-label="📚 Systematik"
            checked
        />
        <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-b-box p-6">
            <div class="space-y-4">
                <h3 class="text-2xl font-bold mb-4">Taxonomische Einordnung</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if ($species->genus && $species->genus->subfamily && $species->genus->subfamily->family)
                        <div>
                            <label class="font-semibold text-sm opacity-75">Familie</label>
                            <p class="text-lg">{{ $species->genus->subfamily->family->name }}</p>
                        </div>
                    @endif

                    @if ($species->genus && $species->genus->subfamily)
                        <div>
                            <label class="font-semibold text-sm opacity-75">Unterfamilie</label>
                            <p class="text-lg">{{ $species->genus->subfamily->name }}</p>
                        </div>
                    @endif

                    @if ($species->genus && $species->genus->tribe)
                        <div>
                            <label class="font-semibold text-sm opacity-75">Tribus</label>
                            <p class="text-lg">{{ $species->genus->tribe->name }}</p>
                        </div>
                    @endif

                    @if ($species->genus)
                        <div>
                            <label class="font-semibold text-sm opacity-75">Gattung</label>
                            <p class="text-lg">{{ $species->genus->name }}</p>
                        </div>
                    @endif

                    @if ($species->genus)
                        <div class="md:col-span-2">
                            <label class="font-semibold text-sm opacity-75">Taxonomiepfad</label>
                            <p class="text-lg">{{ $species->genus->hierarchyPath() }}</p>
                        </div>
                    @elseif ($species->family)
                        <div>
                            <label class="font-semibold text-sm opacity-75">Familie</label>
                            <p class="text-lg">{{ $species->family->name }}</p>
                        </div>
                    @endif

                    <div>
                        <label class="font-semibold text-sm opacity-75">Frisst auch Salbei-Arten</label>
                        <p class="text-lg">{{ $species->sage_feeding_indicator ?? 'keine genaue Angabe' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 2: Habitats -->
        <input
            type="radio"
            name="species_tabs"
            role="tab"
            class="tab"
            aria-label="🏞️ Lebensräume"
        />
        <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-b-box p-6">
            <div class="space-y-4">
                <h3 class="text-2xl font-bold mb-4">Lebensräume</h3>

                @if ($species->habitats->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach ($species->habitats as $habitat)
                            <div class="card bg-base-200">
                                <div class="card-body">
                                    <h4 class="card-title text-lg">{{ $habitat->name }}</h4>
                                    @if ($habitat->description)
                                        <p class="text-sm opacity-75">{{ $habitat->description }}</p>
                                    @endif
                                    @if ($habitat->family)
                                        <span class="badge badge-sm">{{ $habitat->family->name }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div>Keine Lebensräume bekannt</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Tab 3: Plant Associations -->
        <input
            type="radio"
            name="species_tabs"
            role="tab"
            class="tab"
            aria-label="🌿 Pflanzen"
        />
        <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-b-box p-6">
            <div class="space-y-6">
                <h3 class="text-2xl font-bold mb-4">Pflanzliche Verbindungen</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <h4 class="text-xl font-bold mb-4">🌺 Nektarpflanzen</h4>
                        @if ($species->nectarPlants->count() > 0)
                            <ul class="space-y-2">
                                @foreach ($species->nectarPlants as $plant)
                                    <li class="flex items-center gap-2">
                                        <span class="text-lg">🌼</span>
                                        <a href="{{ route('plants.show', $plant) }}" class="link link-primary">
                                            {{ $plant->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-gray-400 italic">Keine Nektarpflanzen bekannt</p>
                        @endif
                    </div>

                    <div>
                        <h4 class="text-xl font-bold mb-4">🥬 Futterpflanzen (Raupen)</h4>
                        @if ($species->larvalHostPlants->count() > 0)
                            <ul class="space-y-2">
                                @foreach ($species->larvalHostPlants as $plant)
                                    <li class="flex items-center gap-2">
                                        <span class="text-lg">🐛</span>
                                        <a href="{{ route('plants.show', $plant) }}" class="link link-primary">
                                            {{ $plant->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-gray-400 italic">Keine Futterpflanzen bekannt</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 4: Distribution -->
        <input
            type="radio"
            name="species_tabs"
            role="tab"
            class="tab"
            aria-label="📍 Verbreitung"
        />
        <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-b-box p-6">
            <div class="space-y-6">
                <h3 class="text-2xl font-bold mb-4">Geografische Verbreitung</h3>

                @if ($species->distributionAreas->count() > 0)
                    <!-- Endangered Areas -->
                    @php
                        $areas = $species->distributionAreas->where('pivot.threatCategory.code', 'VU');
                    @endphp
                    @if ($areas->count() > 0)
                        <div>
                            <h4 class="text-lg font-bold mb-3">⚠️ Gefährdete Regionen</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach ($areas as $area)
                                    <div class="card text-error-content" style="background: {{ $area->pivot->threatCategory->color_code }};">
                                        <div class="card-body py-3">
                                            <p class="text-semibold">{{ $area->name }}</p>
                                            @if ($area->description)
                                                <p class="text-xs opacity-75 mt-2">{{ $area->description }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- All Regions (Complete Distribution) -->
                    <div>
                        <h4 class="text-lg font-bold mb-3">📍 Gesamte Verbreitung</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach ($species->distributionAreas as $region)
                                @php
                                    $status = $region->pivot->threatCategory;
                                    $bgColor = $status->color_code ? $status->color_code : '#cfcfcf';
                                @endphp
                                <div class="card text-primary-content" style="background: {{ $bgColor }}; " >
                                    <div class="card-body py-3">
                                        <p class="font-semibold">{{ $region->code }}</p>
                                        <p class="text-sm">{{ $region->name }}</p>
                                        <p class="text-xs mt-1">
                                            Status: {{ $status->label }}
                                        </p>
                                        @if ($region->description)
                                            <p class="text-xs mt-2">{{ $region->description }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="alert alert-info">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div>Keine Verbreitungsdaten verfügbar</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Tab 5: Map -->
        <input
            type="radio"
            name="species_tabs"
            role="tab"
            class="tab"
            aria-label="🗺️ Karte"
        />
        <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-b-box p-6">
            <div class="space-y-4">
                <h3 class="text-2xl font-bold">Verbreitung auf Karte</h3>
                <livewire:public.regional-distribution-map
                    :species="$species"
                    color-mode="threat"
                    display-mode="all"
                    :key="'species-threat-map-' . $species->id"
                />
            </div>
        </div>
    </div>

    <!-- Life Cycle Calendar -->
    <div class="divider my-8"></div>
    <livewire:public.life-cycle-calendar :species="$species" />
</div>
