<div class="space-y-6">
    <!-- Plant Selection Section -->
    <div class="bg-base-200 p-6 rounded-lg space-y-4">
        <h3 class="text-xl font-bold">🌱 Schritt 1: Wähle deine Gartenpflanzen</h3>

        <div class="form-control">
            <label class="label">
                <span class="label-text font-semibold">Verfügbare Pflanzen</span>
            </label>
            <select
                wire:model.live="selectedPlantIds"
                multiple
                size="6"
                class="select select-bordered w-full"
            >
                @foreach ($plants as $plant)
                    <option value="{{ $plant->id }}">
                        {{ str_repeat('— ', $plant->family->level ?? 0) }}{{ $plant->name }}
                    </option>
                @endforeach
            </select>
            <label class="label">
                <span class="label-text-alt text-xs opacity-75">
                    Mehrere Pflanzen mit Ctrl/Cmd anwählen
                </span>
            </label>
        </div>

        <!-- Selected Plants Display -->
        @if (count($selectedPlantIds) > 0)
            <div class="space-y-2">
                <label class="label">
                    <span class="label-text-alt font-semibold">Ausgewählte Pflanzen ({{ count($selectedPlantIds) }})</span>
                </label>
                <div class="flex flex-wrap gap-2">
                    @foreach ($selectedPlantIds as $plantId)
                        @php
                            $plant = \App\Models\Plant::find($plantId);
                        @endphp
                        @if ($plant)
                            <div class="badge badge-lg badge-primary gap-2">
                                {{ $plant->name }}
                                <button
                                    wire:click="removeSelectedPlant({{ $plantId }})"
                                    class="text-lg hover:opacity-75"
                                    title="Entfernen"
                                >
                                    ✕
                                </button>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <button
                wire:click="clearSelection"
                class="btn btn-sm btn-outline w-full"
                title="Alle Auswahl löschen"
            >
                🔄 Auswahl löschen
            </button>
        @endif
    </div>

    <!-- Results Section -->
    @if ($showResults)
        <div class="space-y-4">
            <h3 class="text-xl font-bold">🦋 Schritt 2: Entdeckte Schmetterlinge</h3>

            @if ($paginatedSpecies->total() > 0)
                <div class="alert alert-success">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <h3 class="font-bold">{{ $paginatedSpecies->total() }} Schmetterlinge gefunden!</h3>
                        <div class="text-sm">Diese Schmetterlinge nutzen deine ausgewählten Pflanzen.</div>
                    </div>
                </div>

                <!-- Results Table -->
                <div class="overflow-x-auto">
                    <table class="table w-full table-sm md:table-md">
                        <thead>
                            <tr class="bg-base-200">
                                <th>Name</th>
                                <th>Code</th>
                                <th>Pflanzenverhältnis</th>
                                <th>Gefährdet</th>
                                <th>Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($paginatedSpecies as $species)
                                <tr class="hover">
                                    <td class="font-semibold">{{ $species->name }}</td>
                                    <td class="font-mono text-sm">{{ $species->code }}</td>
                                    <td class="text-sm">
                                        @php
                                            $uses = $this->getPlantUseForSpecies($species);
                                        @endphp
                                        <div class="space-y-1">
                                            @foreach ($uses as $use)
                                                <span class="badge badge-sm">
                                                    @if ($use === 'Nektarpflanze')
                                                        🌺 {{ $use }}
                                                    @else
                                                        🥬 {{ $use }}
                                                    @endif
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $threatenedCount = $species->distributionAreas
                                                ->filter(fn ($area) => optional($area->pivot->threatCategory)->code === 'VU')
                                                ->count();
                                        @endphp
                                        @if ($threatenedCount > 0)
                                            <span class="badge badge-error badge-sm">
                                                {{ $threatenedCount }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a
                                            href="{{ route('species.show', $species) }}"
                                            class="btn btn-xs btn-primary"
                                            title="Art ansehen"
                                        >
                                            👁️ Ansehen
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($paginatedSpecies->hasPages())
                    <div class="flex justify-center py-6">
                        {{ $paginatedSpecies->links(data: ['scrollTo' => false]) }}
                    </div>
                @endif
            @else
                <div class="alert alert-warning">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v2m0 0H8m4 0h4"></path></svg>
                    <div>
                        <h3 class="font-bold">Keine Schmetterlinge gefunden</h3>
                        <div class="text-sm">
                            Leider nutzen keine bekannten Schmetterlinge deine ausgewählten Pflanzen.
                            Versuche andere Pflanzen auszuwählen.
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @else
        <div class="alert alert-info">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div>
                <h3 class="font-bold">Bereit zu entdecken?</h3>
                <div class="text-sm">
                    Wähle oben deine Gartenpflanzen aus, um zu sehen, welche Schmetterlinge sie anlocken!
                </div>
            </div>
        </div>
    @endif
</div>
