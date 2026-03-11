@php
    $componentId = 'regional-map-' . $this->getId();
    $areasWithoutGeometry = collect($areaData)->filter(fn ($item) => !($item['geometry_available'] ?? false));
    $mapConfig = [
        'speciesId' => $speciesId,
        'colorMode' => $colorMode,
        'displayMode' => $displayMode,
        'geometryUrlTemplate' => url('/api/map/areas/__CODE__/geometry'),
        'metaUrlTemplate' => url('/api/map/areas/__CODE__/meta'),
        'cacheTtlMs' => 86400000,
    ];
@endphp

<div class="space-y-6">
    <div class="bg-base-200 p-4 rounded-lg space-y-4">
        @if ($colorMode === 'count')
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
        @else
            <div class="text-sm">
                <p class="font-semibold">Darstellung:</p>
                <p class="opacity-75">Flächenfarbe entspricht dem Gefährdungsstatus je Gebiet.</p>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-4 gap-4 items-start">
        <div class="xl:col-span-3 space-y-3">
            <p class="text-sm text-gray-500">
                @if ($colorMode === 'threat')
                    Interaktive GeoJSON-Karte: Farbgebung nach Gefährdungscode der aktuellen Art.
                @else
                    Interaktive GeoJSON-Karte: Klick auf ein Gebiet zeigt Name, Code und Anzahl.
                @endif
            </p>
            <div wire:ignore class="rounded-lg overflow-hidden border border-base-300 bg-base-100">
                <div
                    id="{{ $componentId }}-canvas"
                    class="w-full"
                    style="height: 680px; min-height: 480px;"
                ></div>
            </div>
        </div>

        <aside class="xl:col-span-1 rounded-lg border border-base-300 bg-base-100 p-3">
            <h3 class="font-semibold mb-3">Gebiete</h3>
            <div
                id="{{ $componentId }}-area-list"
                class="space-y-2 max-h-[680px] overflow-y-auto pr-1"
            >
                @foreach ($areaData as $data)
                    <button
                        type="button"
                        data-area-code="{{ $data['code'] }}"
                        class="regional-map-area-button w-full text-left p-3 rounded border transition border-base-300 hover:border-base-content/30"
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
                        @if ($colorMode === 'threat' && $data['threat_code'])
                            <div class="text-xs mt-2">
                                <span
                                    class="badge badge-sm text-base-100"
                                    style="background-color: {{ $data['threat_color'] ?? '#6b7280' }};"
                                >
                                    {{ $data['threat_code'] }}{{ $data['threat_label'] ? ' - ' . $data['threat_label'] : '' }}
                                </span>
                            </div>
                        @endif
                    </button>
                @endforeach
            </div>
        </aside>
    </div>

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
                Dunklere Flächen bedeuten mehr Arten in einem Gebiet. Beim Klick auf eine Fläche erscheinen die Detaildaten ohne GeoJSON-Neuladen.
            </div>
        </div>
    </div>

    <script id="{{ $componentId }}-area-data" type="application/json">@json($areaData)</script>
    <script id="{{ $componentId }}-config" type="application/json">@json($mapConfig)</script>

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
            const componentId = @json($componentId);
            const mapContainerId = componentId + '-canvas';
            const areaDataNodeId = componentId + '-area-data';
            const configNodeId = componentId + '-config';
            const listContainerId = componentId + '-area-list';
            const selectedButtonClasses = ['border-primary', 'bg-primary/10'];
            const geometryCachePrefix = 'regional-map-geometry:';
            let map = null;
            let selectedAreaCode = null;
            let metaAbortController = null;
            const areaLayers = new Map();
            const geometryRequests = new Map();

            function readJsonScript(nodeId, fallback) {
                const node = document.getElementById(nodeId);
                if (!node) {
                    return fallback;
                }

                try {
                    return JSON.parse(node.textContent || '');
                } catch {
                    return fallback;
                }
            }

            function getAreaData() {
                return readJsonScript(areaDataNodeId, []);
            }

            function getConfig() {
                return readJsonScript(configNodeId, {});
            }

            function areaMap() {
                return new Map(getAreaData().map(function (area) {
                    return [area.code, area];
                }));
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

            function cacheKey(code) {
                return geometryCachePrefix + code;
            }

            function readGeometryCache(code) {
                try {
                    const raw = window.localStorage.getItem(cacheKey(code));
                    if (!raw) {
                        return null;
                    }

                    const parsed = JSON.parse(raw);
                    if (!parsed || parsed.expiresAt <= Date.now() || !parsed.geometry) {
                        window.localStorage.removeItem(cacheKey(code));
                        return null;
                    }

                    return parsed;
                } catch {
                    return null;
                }
            }

            function writeGeometryCache(code, geometry, etag) {
                const config = getConfig();
                try {
                    window.localStorage.setItem(cacheKey(code), JSON.stringify({
                        geometry: geometry,
                        etag: etag || null,
                        expiresAt: Date.now() + (config.cacheTtlMs || 86400000),
                    }));
                } catch {
                    // Ignore cache write failures.
                }
            }

            async function fetchGeometry(code) {
                const cached = readGeometryCache(code);
                if (cached?.geometry) {
                    return cached.geometry;
                }

                if (geometryRequests.has(code)) {
                    return geometryRequests.get(code);
                }

                const config = getConfig();
                const url = (config.geometryUrlTemplate || '').replace('__CODE__', encodeURIComponent(code));
                const headers = {};
                if (cached?.etag) {
                    headers['If-None-Match'] = cached.etag;
                }

                const request = fetch(url, { headers: headers })
                    .then(async function (response) {
                        if (response.status === 304 && cached?.geometry) {
                            return cached.geometry;
                        }

                        if (!response.ok) {
                            throw new Error('Geometry request failed for ' + code);
                        }

                        const payload = await response.json();
                        const geometry = payload?.data?.geometry || null;
                        if (!geometry) {
                            throw new Error('Missing geometry for ' + code);
                        }

                        writeGeometryCache(code, geometry, response.headers.get('ETag'));
                        return geometry;
                    })
                    .finally(function () {
                        geometryRequests.delete(code);
                    });

                geometryRequests.set(code, request);
                return request;
            }

            function resolveAreaStyle(area) {
                const isSelected = selectedAreaCode !== null && selectedAreaCode === area.code;

                return {
                    color: isSelected ? '#111827' : '#1f2937',
                    weight: isSelected ? 3 : 1,
                    fillColor: area.fill_color || '#e5e7eb',
                    fillOpacity: isSelected ? 0.9 : 0.75,
                };
            }

            function syncAreaButtonSelection() {
                const list = document.getElementById(listContainerId);
                if (!list) {
                    return;
                }

                list.querySelectorAll('[data-area-code]').forEach(function (button) {
                    const isSelected = button.getAttribute('data-area-code') === selectedAreaCode;
                    selectedButtonClasses.forEach(function (className) {
                        button.classList.toggle(className, isSelected);
                    });
                });
            }

            function updateLayerStyle(code) {
                const area = areaMap().get(code);
                const layer = areaLayers.get(code);
                if (!area || !layer) {
                    return;
                }

                layer.setStyle(resolveAreaStyle(area));
            }

            function updateAllLayerStyles() {
                areaLayers.forEach(function (_, code) {
                    updateLayerStyle(code);
                });
                syncAreaButtonSelection();
            }

            function buildPopupContent(area, meta) {
                const lines = ['<strong>' + (area?.name || meta?.name || 'Unbekanntes Gebiet') + '</strong>'];
                lines.push('Code: ' + (area?.code || meta?.code || '—'));

                if (meta?.species) {
                    if (meta.species.threat_status) {
                        const threat = meta.species.threat_status;
                        lines.push('Status: ' + threat.code + (threat.label ? ' (' + threat.label + ')' : ''));
                    } else {
                        lines.push('Status: keine artspezifischen Daten');
                    }
                } else if (typeof meta?.species_distribution_area_count === 'number') {
                    lines.push('Anzahl: ' + meta.species_distribution_area_count);
                } else if (typeof area?.count === 'number') {
                    lines.push('Anzahl: ' + area.count);
                }

                return lines.join('<br>');
            }

            async function fetchMeta(code) {
                const config = getConfig();
                const url = new URL((config.metaUrlTemplate || '').replace('__CODE__', encodeURIComponent(code)), window.location.origin);
                if (config.speciesId) {
                    url.searchParams.set('species_id', String(config.speciesId));
                }

                if (metaAbortController) {
                    metaAbortController.abort();
                }

                metaAbortController = new AbortController();
                const response = await fetch(url.toString(), { signal: metaAbortController.signal });
                if (!response.ok) {
                    throw new Error('Meta request failed for ' + code);
                }

                const payload = await response.json();
                return payload?.data || null;
            }

            async function openAreaDetails(code, options) {
                const settings = options || {};
                const area = areaMap().get(code);
                if (!area) {
                    return;
                }

                selectedAreaCode = code;
                updateAllLayerStyles();

                let layer = areaLayers.get(code);
                if (!layer && area.geometry_available) {
                    try {
                        await ensureAreaLayer(area);
                        layer = areaLayers.get(code);
                    } catch {
                        layer = null;
                    }
                }

                if (layer) {
                    if (settings.flyTo !== false) {
                        const bounds = layer.getBounds();
                        if (bounds.isValid()) {
                            ensureMap()?.fitBounds(bounds.pad(0.15));
                        }
                    }

                    layer.bindPopup(buildPopupContent(area, null)).openPopup();
                }

                try {
                    const meta = await fetchMeta(code);
                    if (selectedAreaCode !== code) {
                        return;
                    }

                    if (layer) {
                        layer.bindPopup(buildPopupContent(area, meta)).openPopup();
                    }
                } catch (error) {
                    if (error?.name === 'AbortError') {
                        return;
                    }

                    if (layer) {
                        layer.bindPopup(buildPopupContent(area, null)).openPopup();
                    }
                }
            }

            async function ensureAreaLayer(area) {
                if (!area.geometry_available || areaLayers.has(area.code)) {
                    return;
                }

                const mapInstance = ensureMap();
                if (!mapInstance) {
                    return;
                }

                const geometry = await fetchGeometry(area.code);
                const feature = {
                    type: 'Feature',
                    geometry: geometry,
                    properties: {
                        code: area.code,
                    },
                };

                const layer = L.geoJSON(feature, {
                    style: function () {
                        return resolveAreaStyle(area);
                    },
                    onEachFeature: function (_, featureLayer) {
                        featureLayer.on('click', function () {
                            openAreaDetails(area.code, { flyTo: false });
                        });
                    }
                });

                layer.addTo(mapInstance);
                areaLayers.set(area.code, layer);
            }

            async function syncMapLayers() {
                const areas = getAreaData();
                const mapInstance = ensureMap();
                if (!mapInstance) {
                    return;
                }

                await Promise.allSettled(areas.map(function (area) {
                    return ensureAreaLayer(area);
                }));

                updateAllLayerStyles();

                const group = L.featureGroup(Array.from(areaLayers.values()));
                const bounds = group.getBounds();
                if (bounds.isValid()) {
                    mapInstance.fitBounds(bounds.pad(0.15));
                }
            }

            function attachAreaListHandler() {
                const list = document.getElementById(listContainerId);
                if (!list || list.dataset.bound === 'true') {
                    return;
                }

                list.dataset.bound = 'true';
                list.addEventListener('click', function (event) {
                    const button = event.target.closest('[data-area-code]');
                    if (!button) {
                        return;
                    }

                    openAreaDetails(button.getAttribute('data-area-code'));
                });
            }

            function showLeafletUnavailable() {
                const container = document.getElementById(mapContainerId);
                if (!container) {
                    return;
                }

                container.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;padding:1rem;color:#6b7280;">Karte konnte nicht geladen werden (Leaflet nicht verfügbar).</div>';
            }

            function boot() {
                attachAreaListHandler();
                if (typeof L === 'undefined') {
                    showLeafletUnavailable();
                    return;
                }

                syncMapLayers().then(function () {
                    setTimeout(function () {
                        if (map) {
                            map.invalidateSize();
                        }
                    }, 50);
                });
            }

            function bootWhenReady(attemptsLeft) {
                const remaining = typeof attemptsLeft === 'number' ? attemptsLeft : 20;
                if (typeof L !== 'undefined') {
                    boot();
                    return;
                }

                if (remaining <= 0) {
                    showLeafletUnavailable();
                    return;
                }

                setTimeout(function () {
                    bootWhenReady(remaining - 1);
                }, 100);
            }

            bootWhenReady();
            document.addEventListener('DOMContentLoaded', boot);
            window.addEventListener('load', function () {
                bootWhenReady();
            });
            document.addEventListener('livewire:navigated', boot);
            window.addEventListener('regional-map-data-updated', function (event) {
                if (event?.detail?.speciesId !== getConfig().speciesId) {
                    return;
                }

                syncMapLayers();
            });
        })();
    </script>
</div>
