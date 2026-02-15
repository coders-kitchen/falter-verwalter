<div class="space-y-8">
    <!-- Header Section -->
    <div class="space-y-4">
        <h1 class="text-5xl font-bold">🌿 {{ $plant->name }}</h1>
        <p class="text-lg opacity-75">{{ $plant->code ?? 'Pflanze' }}</p>

        @if ($plant->description)
            <p class="text-base leading-relaxed max-w-3xl">{{ $plant->description }}</p>
        @endif
    </div>

    <!-- Tabs/Sections -->
    <div class="tabs tabs-lifted" role="tablist">
        <!-- Tab 1: Taxonomy -->
        <input
            type="radio"
            name="plant_tabs"
            role="tab"
            class="tab"
            aria-label="📚 Systematik"
            checked
        />
        <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-b-box p-6">
            <div class="space-y-4">
                <h3 class="text-2xl font-bold mb-4">Systematische Einordnung</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if ($plant->family)
                        <div>
                            <label class="font-semibold text-sm opacity-75">Familie</label>
                            <p class="text-lg">{{ $plant->family->name }}</p>
                        </div>
                    @endif

                    @if ($plant->code)
                        <div>
                            <label class="font-semibold text-sm opacity-75">Code</label>
                            <p class="text-lg font-mono">{{ $plant->code }}</p>
                        </div>
                    @endif
                </div>

                <div class="divider my-6">Ökologische Zeigerwerte</div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="flex justify-between bg-base-200 rounded px-3 py-2"><span>Lichtzahl</span><span class="font-semibold">{{ $plant->indicatorDisplay('light_number') }}</span></div>
                    <div class="flex justify-between bg-base-200 rounded px-3 py-2"><span>Salzzahl</span><span class="font-semibold">{{ $plant->indicatorDisplay('salt_number') }}</span></div>
                    <div class="flex justify-between bg-base-200 rounded px-3 py-2"><span>Temperaturzahl</span><span class="font-semibold">{{ $plant->indicatorDisplay('temperature_number') }}</span></div>
                    <div class="flex justify-between bg-base-200 rounded px-3 py-2"><span>Kontinentalitätszahl</span><span class="font-semibold">{{ $plant->indicatorDisplay('continentality_number') }}</span></div>
                    <div class="flex justify-between bg-base-200 rounded px-3 py-2"><span>Reaktionszahl</span><span class="font-semibold">{{ $plant->indicatorDisplay('reaction_number') }}</span></div>
                    <div class="flex justify-between bg-base-200 rounded px-3 py-2"><span>Feuchtezahl</span><span class="font-semibold">{{ $plant->indicatorDisplay('moisture_number') }}</span></div>
                    <div class="flex justify-between bg-base-200 rounded px-3 py-2"><span>Feuchtewechsel</span><span class="font-semibold">{{ $plant->indicatorDisplay('moisture_variation') }}</span></div>
                    <div class="flex justify-between bg-base-200 rounded px-3 py-2"><span>Stickstoffzahl</span><span class="font-semibold">{{ $plant->indicatorDisplay('nitrogen_number') }}</span></div>
                </div>

                <div class="mt-4">
                    <div class="flex justify-between bg-base-200 rounded px-3 py-2">
                        <span>Schwermetallresistenz</span>
                        <span class="font-semibold">{{ $plant->heavy_metal_resistance ?? '—' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 2: Habitats -->
        <input
            type="radio"
            name="plant_tabs"
            role="tab"
            class="tab"
            aria-label="🏞️ Lebensräume"
        />
        <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-b-box p-6">
            <div class="space-y-4">
                <h3 class="text-2xl font-bold mb-4">Lebensräume</h3>

                @if ($plant->habitats->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach ($plant->habitats as $habitat)
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

        <!-- Tab 3: Associated Butterflies -->
        <input
            type="radio"
            name="plant_tabs"
            role="tab"
            class="tab"
            aria-label="🦋 Schmetterlinge"
        />
        <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-b-box p-6">
            <div class="space-y-8">
                <h3 class="text-2xl font-bold mb-4">Schmetterlinge, die diese Pflanze nutzen</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Nectar Plants -->
                    <div>
                        <h4 class="text-xl font-bold mb-4">🌺 Nektarpflanze für</h4>
                        @if ($nectarSpecies->count() > 0)
                            <ul class="space-y-2">
                                @foreach ($nectarSpecies as $species)
                                    <li class="flex items-center gap-2">
                                        <span class="text-lg">🦋</span>
                                        <a
                                            href="{{ route('species.show', $species) }}"
                                            class="link link-primary"
                                        >
                                            {{ $species->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-gray-400 italic">Keine Schmetterlinge bekannt</p>
                        @endif
                    </div>

                    <!-- Larval Host Plants -->
                    <div>
                        <h4 class="text-xl font-bold mb-4">🥬 Futterpflanze (Raupenfutter) für</h4>
                        @if ($larvalSpecies->count() > 0)
                            <ul class="space-y-2">
                                @foreach ($larvalSpecies as $species)
                                    <li class="flex items-center gap-2">
                                        <span class="text-lg">🐛</span>
                                        <a
                                            href="{{ route('species.show', $species) }}"
                                            class="link link-primary"
                                        >
                                            {{ $species->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-gray-400 italic">Keine Schmetterlinge bekannt</p>
                        @endif
                    </div>
                </div>

                @if ($nectarSpecies->count() === 0 && $larvalSpecies->count() === 0)
                    <div class="alert alert-warning">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v2m0 0H8m4 0h4"></path></svg>
                        <div>
                            <h3 class="font-bold">Keine Schmetterlinge bekannt</h3>
                            <div class="text-sm">
                                Es sind noch keine Schmetterlinge registriert, die diese Pflanze nutzen.
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Info Box -->
    <div class="alert alert-info">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <div>
            <h3 class="font-bold">Pflanzen im Garten</h3>
            <div class="text-sm">
                Möchtest du wissen, welche Schmetterlinge deine Gartenpflanzen anlocken?
                <a href="{{ route('discover.index') }}" class="link link-primary">
                    Nutze unseren Pflanzen-Filter!
                </a>
            </div>
        </div>
    </div>
</div>
