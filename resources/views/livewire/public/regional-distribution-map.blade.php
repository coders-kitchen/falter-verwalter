<div class="space-y-6">
    <!-- Control Panel -->
    <div class="bg-base-200 p-4 rounded-lg space-y-4">
        <label class="label">
            <span class="label-text font-semibold">Anzeigemodus:</span>
        </label>
        <div class="flex flex-wrap gap-2">
            <button
                wire:click="toggleDisplayMode('endangered')"
                class="btn {{ $displayMode === 'endangered' ? 'btn-primary' : 'btn-outline' }} btn-sm"
            >
                ⚠️ Gefährdete Arten
            </button>
            <button
                wire:click="toggleDisplayMode('all')"
                class="btn {{ $displayMode === 'all' ? 'btn-primary' : 'btn-outline' }} btn-sm"
            >
                🦋 Alle Arten
            </button>
        </div>

        <!-- Legend -->
        <div class="space-y-2 mt-4">
            <p class="text-sm font-semibold">Farbintensität (von hell zu dunkel):</p>
            <div class="space-y-1 text-xs">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 bg-gray-200 border border-gray-300"></div>
                    <span>Keine Arten</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 bg-yellow-200"></div>
                    <span>Wenige Arten (1-20%)</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 bg-yellow-400"></div>
                    <span>Einige Arten (20-40%)</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 bg-orange-400"></div>
                    <span>Viele Arten (40-60%)</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 bg-orange-600"></div>
                    <span>Sehr viele Arten (60-80%)</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 bg-red-600"></div>
                    <span>Maximale Arten (80-100%)</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Map Display -->
    <div class="space-y-4">
        <p class="text-sm text-gray-500">
            Klicke auf ein Verbreitungsgebiet, um es hervorzuheben
        </p>

        <!-- Distribution Areas Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($areaData as $data)
                <button
                    wire:click="selectArea({{ $data['id'] }})"
                    class="p-4 rounded-lg border-2 border-base-300 transition-all hover:border-primary {{ $selectedArea === $data['id'] ? 'border-primary ring-2 ring-primary' : '' }} {{ $this->getColorIntensity($data['count']) }}"
                    title="Klicken zum Filtern nach {{ $data['name'] }}"
                >
                    <div class="flex flex-col items-start h-full">
                        <p class="text-sm font-semibold opacity-75">{{ $data['name'] }}</p>
                        <div class="mt-auto">
                            @if ($data['count'] > 0)
                                <p class="text-2xl font-bold">{{ $data['count'] }}</p>
                                <p class="text-xs opacity-75">
                                    {{ $displayMode === 'endangered' ? 'gefährdete Arten' : 'Arten' }}
                                </p>
                            @else
                                <p class="text-lg opacity-50">—</p>
                                <p class="text-xs opacity-75">Keine Arten</p>
                            @endif
                        </div>
                    </div>
                </button>
            @endforeach
        </div>
    </div>

    <!-- Info Box -->
    <div class="alert alert-info">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <div>
            <h3 class="font-bold">Über die Verbreitungsgebiete</h3>
            <div class="text-sm">
                Die Daten zeigen die Verbreitung von Schmetterlingsarten in verschiedenen Verbreitungsgebieten.
                Dunklere Farben bedeuten mehr Arten in einem Gebiet.
            </div>
        </div>
    </div>
</div>
