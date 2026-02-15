<div class="space-y-6">
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
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-4 gap-4 items-start">
        <div class="xl:col-span-3 space-y-3">
            <p class="text-sm text-gray-500">
                Interaktive GeoJSON-Karte: Klick auf ein Gebiet zeigt Name, Code und Anzahl.
            </p>
            <div wire:ignore class="rounded-lg overflow-hidden border border-base-300 bg-base-100">
                <div
                    id="regional-distribution-map-canvas"
                    class="w-full"
                    style="height: 680px; min-height: 480px;"
                ></div>
            </div>
        </div>

        <aside class="xl:col-span-1 rounded-lg border border-base-300 bg-base-100 p-3">
            <h3 class="font-semibold mb-3">Gebiete</h3>
            <div class="space-y-2 max-h-[680px] overflow-y-auto pr-1">
                @foreach ($areaData as $data)
                    <button
                        type="button"
                        wire:click="selectArea({{ $data['id'] }})"
                        class="w-full text-left p-3 rounded border transition
                            {{ $selectedArea === $data['id'] ? 'border-primary bg-primary/10' : 'border-base-300 hover:border-base-content/30' }}"
                    >
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-medium">{{ $data['name'] }}</span>
                            <span class="badge badge-neutral">{{ $data['count'] }}</span>
                        </div>
                        <div class="text-xs opacity-70 mt-1">
                            <code>{{ $data['code'] ?? '—' }}</code>
                            @if (!$data['geometry_available'])
                                <span class="ml-2 text-warning">kein GeoJSON</span>
                            @endif
                        </div>
                    </button>
                @endforeach
            </div>
        </aside>
    </div>

    @php
        $areasWithoutGeometry = collect($areaData)->filter(fn ($item) => !($item['geometry_available'] ?? false));
    @endphp

    @if ($areasWithoutGeometry->count() > 0)
        <div class="alert alert-warning">
            <div>
                <h3 class="font-bold">Geometrie fehlt für {{ $areasWithoutGeometry->count() }} Gebiet(e)</h3>
                <div class="text-sm">
                    Diese Gebiete werden aktuell nicht auf der Karte gezeichnet und benötigen ein GeoJSON-Polygon.
                </div>
            </div>
        </div>
    @endif

    <div class="alert alert-info">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <div>
            <h3 class="font-bold">Über die Verbreitungsgebiete</h3>
            <div class="text-sm">
                Dunklere Flächen bedeuten mehr Arten in einem Gebiet. Beim Klick auf eine Fläche erscheint die Detailzahl.
            </div>
        </div>
    </div>

    <script id="regional-map-payload" type="application/json">@json($mapPayload)</script>

    @once
        <link
            rel="stylesheet"
            href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            crossorigin=""
        />
        <script
            src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            crossorigin=""
        ></script>
    @endonce

    <script>
        (function () {
            const mapContainerId = 'regional-distribution-map-canvas';
            let map = null;
            let geoJsonLayer = null;
            let selectedArea = @json($selectedArea);

            function readPayload() {
                const node = document.getElementById('regional-map-payload');
                if (!node) {
                    return { type: 'FeatureCollection', features: [] };
                }

                try {
                    return JSON.parse(node.textContent || '{}');
                } catch {
                    return { type: 'FeatureCollection', features: [] };
                }
            }

            function ensureMap() {
                const container = document.getElementById(mapContainerId);
                if (!container || typeof L === 'undefined') {
                    return null;
                }

                if (!map) {
                    map = L.map(mapContainerId, {
                        zoomControl: true,
                        scrollWheelZoom: true,
                    });

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 10,
                        minZoom: 5,
                        attribution: '&copy; OpenStreetMap-Mitwirkende',
                    }).addTo(map);

                    map.setView([51.2, 10.4], 6);
                }

                return map;
            }

            function renderGeoJson(payload) {
                const mapInstance = ensureMap();
                if (!mapInstance) {
                    return;
                }

                if (geoJsonLayer) {
                    geoJsonLayer.remove();
                    geoJsonLayer = null;
                }

                if (!payload || !Array.isArray(payload.features) || payload.features.length === 0) {
                    return;
                }

                geoJsonLayer = L.geoJSON(payload, {
                    style: function (feature) {
                        const featureId = feature?.properties?.id;
                        const isSelected = selectedArea !== null && Number(selectedArea) === Number(featureId);
                        return {
                            color: isSelected ? '#111827' : '#1f2937',
                            weight: isSelected ? 3 : 1,
                            fillColor: feature?.properties?.color || '#e5e7eb',
                            fillOpacity: isSelected ? 0.9 : 0.75,
                        };
                    },
                    onEachFeature: function (feature, layer) {
                        const name = feature?.properties?.name || 'Unbekanntes Gebiet';
                        const code = feature?.properties?.code || '—';
                        const count = feature?.properties?.count ?? 0;
                        layer.bindPopup('<strong>' + name + '</strong><br>Code: ' + code + '<br>Anzahl: ' + count);
                    }
                }).addTo(mapInstance);

                const bounds = geoJsonLayer.getBounds();
                if (bounds.isValid()) {
                    mapInstance.fitBounds(bounds.pad(0.15));
                }
            }

            function boot() {
                const payload = readPayload();
                renderGeoJson(payload);
            }

            function bootWhenReady(attemptsLeft = 20) {
                const container = document.getElementById(mapContainerId);
                if (!container) {
                    return;
                }

                if (typeof L !== 'undefined') {
                    boot();
                    setTimeout(function () {
                        if (map) {
                            map.invalidateSize();
                        }
                    }, 50);
                    return;
                }

                if (attemptsLeft > 0) {
                    setTimeout(function () {
                        bootWhenReady(attemptsLeft - 1);
                    }, 100);
                    return;
                }

                container.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;padding:1rem;color:#6b7280;">Karte konnte nicht geladen werden (Leaflet nicht verfügbar).</div>';
            }

            bootWhenReady();
            document.addEventListener('DOMContentLoaded', boot);
            window.addEventListener('load', bootWhenReady);
            document.addEventListener('livewire:navigated', boot);
            window.addEventListener('regional-map-data-updated', function (event) {
                const payload = event?.detail?.payload;
                selectedArea = event?.detail?.selectedArea ?? null;
                renderGeoJson(payload);
            });
        })();
    </script>
</div>
