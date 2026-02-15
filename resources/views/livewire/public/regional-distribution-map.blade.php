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

    <div class="space-y-4">
        <p class="text-sm text-gray-500">
            Interaktive GeoJSON-Karte: Gebiete mit hinterlegten Polygonen werden direkt auf der Karte dargestellt.
        </p>

        <div wire:ignore class="rounded-lg overflow-hidden border border-base-300 bg-base-100">
            <div id="regional-distribution-map-canvas" class="w-full h-[620px]"></div>
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
    </div>

    <div class="overflow-x-auto">
        <table class="table table-zebra">
            <thead>
                <tr>
                    <th>Gebiet</th>
                    <th>Code</th>
                    <th>Anzahl</th>
                    <th>GeoJSON</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($areaData as $data)
                    <tr>
                        <td>{{ $data['name'] }}</td>
                        <td><code>{{ $data['code'] ?? '—' }}</code></td>
                        <td>{{ $data['count'] }}</td>
                        <td>
                            @if ($data['geometry_available'])
                                <span class="badge badge-success badge-sm">vorhanden</span>
                            @else
                                <span class="badge badge-ghost badge-sm">fehlt</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

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
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
            crossorigin=""
        />
        <script
            src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""
        ></script>
    @endonce

    <script>
        (function () {
            const mapContainerId = 'regional-distribution-map-canvas';
            let map = null;
            let geoJsonLayer = null;

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
                        return {
                            color: '#1f2937',
                            weight: 1,
                            fillColor: feature?.properties?.color || '#e5e7eb',
                            fillOpacity: 0.75,
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

            document.addEventListener('DOMContentLoaded', boot);
            document.addEventListener('livewire:navigated', boot);
            window.addEventListener('regional-map-data-updated', function (event) {
                const payload = event?.detail?.payload;
                renderGeoJson(payload);
            });
        })();
    </script>
</div>
