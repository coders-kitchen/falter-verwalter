<?php

namespace App\Livewire\Public;

use App\Models\DistributionArea;
use App\Models\Species;
use App\Models\SpeciesDistributionArea;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class RegionalDistributionMap extends Component
{
    public $species = null;
    public $displayMode = 'all'; // 'all' or 'endangered'
    public $colorMode = 'count'; // 'count' or 'threat'
    public $areaData = [];
    public $selectedArea = null;
    public $maxCount = 0;
    public $mapPayload = [
        'type' => 'FeatureCollection',
        'features' => [],
    ];

    public function mount($species = null, string $displayMode = 'all', string $colorMode = 'count')
    {
        $this->species = $species;
        $this->displayMode = in_array($displayMode, ['all', 'endangered'], true) ? $displayMode : 'all';
        $this->colorMode = in_array($colorMode, ['count', 'threat'], true) ? $colorMode : 'count';
        $this->aggregateRegionData();
    }

    public function toggleDisplayMode($mode)
    {
        $this->displayMode = $mode;
        $this->aggregateRegionData();
        $this->dispatchMapUpdate();
    }

    public function selectArea($areaId)
    {
        $this->selectedArea = $this->selectedArea === $areaId ? null : $areaId;
        $this->dispatchMapUpdate();
    }

    public function aggregateRegionData()
    {
        $areas = DistributionArea::query()
            ->select(['id', 'name', 'code', 'geojson_path'])
            ->orderBy('name')
            ->get();
        $countsByAreaId = $this->loadCountsByAreaId();
        $threatByAreaId = $this->loadThreatByAreaId();

        $this->areaData = [];
        $this->maxCount = 0;
        $features = [];

        foreach ($areas as $area) {
            $count = (int) ($countsByAreaId[$area->id] ?? 0);
            $geometry = $this->resolveAreaGeometry($area);
            $threat = $threatByAreaId[$area->id] ?? null;
            $featureColor = $this->resolveFeatureColor($count, $threat);

            $this->areaData[$area->id] = [
                'name' => $area->name,
                'count' => $count,
                'id' => $area->id,
                'code' => $area->code,
                'geometry_available' => is_array($geometry),
                'threat_code' => $threat['code'] ?? null,
                'threat_label' => $threat['label'] ?? null,
                'threat_color' => $threat['color_code'] ?? null,
            ];

            if ($count > $this->maxCount) {
                $this->maxCount = $count;
            }

            if (is_array($geometry) && in_array($geometry['type'] ?? null, ['Polygon', 'MultiPolygon'], true)) {
                $features[] = [
                    'type' => 'Feature',
                    'geometry' => $geometry,
                    'properties' => [
                        'id' => $area->id,
                        'name' => $area->name,
                        'code' => $area->code,
                        'count' => $count,
                        'color' => $featureColor,
                        'threat_code' => $threat['code'] ?? null,
                        'threat_label' => $threat['label'] ?? null,
                    ],
                ];
            }
        }

        $this->mapPayload = [
            'type' => 'FeatureCollection',
            'features' => $features,
        ];
    }

    private function loadCountsByAreaId(): array
    {
        return SpeciesDistributionArea::query()
            ->selectRaw('distribution_area_id, COUNT(*) as aggregate_count')
            ->when($this->species, function ($query) {
                $query->where('species_id', $this->species->id);
            })
            ->when($this->displayMode === 'endangered', function ($query) {
                $query->whereHas('threatCategory', function ($threatQuery) {
                    $threatQuery->where('code', 'VU');
                });
            })
            ->groupBy('distribution_area_id')
            ->pluck('aggregate_count', 'distribution_area_id')
            ->map(fn ($count) => (int) $count)
            ->all();
    }

    private function loadThreatByAreaId(): array
    {
        if (!$this->species) {
            return [];
        }

        return SpeciesDistributionArea::query()
            ->with('threatCategory:id,code,label,color_code')
            ->where('species_id', $this->species->id)
            ->get()
            ->keyBy('distribution_area_id')
            ->map(function (SpeciesDistributionArea $item) {
                $threat = $item->threatCategory;
                if (!$threat) {
                    return null;
                }

                return [
                    'code' => $threat->code,
                    'label' => $threat->label,
                    'color_code' => $threat->color_code,
                ];
            })
            ->filter()
            ->all();
    }

    private function resolveFeatureColor(int $count, ?array $threat): string
    {
        if ($this->colorMode === 'threat' && $threat && !empty($threat['color_code'])) {
            return (string) $threat['color_code'];
        }

        return $this->getColorHex($count);
    }

    private function resolveAreaGeometry(DistributionArea $area): ?array
    {
        if ($area->geojson_path && Storage::disk('public')->exists($area->geojson_path)) {
            $raw = Storage::disk('public')->get($area->geojson_path);
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                return $this->extractGeometryFromGeoJson($decoded);
            }
        }

        return null;
    }

    private function extractGeometryFromGeoJson(array $decoded): ?array
    {
        $type = $decoded['type'] ?? null;

        if ($type === 'Feature') {
            $geometry = $decoded['geometry'] ?? null;
            return is_array($geometry) ? $this->extractGeometryFromGeoJson($geometry) : null;
        }

        if ($type === 'FeatureCollection') {
            $features = $decoded['features'] ?? null;
            if (!is_array($features) || count($features) === 0 || !is_array($features[0])) {
                return null;
            }

            return $this->extractGeometryFromGeoJson($features[0]);
        }

        if (!in_array($type, ['Polygon', 'MultiPolygon'], true)) {
            return null;
        }

        $coordinates = $decoded['coordinates'] ?? null;
        if (!is_array($coordinates) || count($coordinates) === 0) {
            return null;
        }

        return [
            'type' => $type,
            'coordinates' => $coordinates,
        ];
    }

    public function getColorIntensity($count)
    {
        if ($this->maxCount === 0) {
            return 'bg-gray-200';
        }

        $percentage = ($count / $this->maxCount) * 100;

        if ($percentage === 0) {
            return 'bg-gray-200';
        } elseif ($percentage < 20) {
            return 'bg-yellow-200';
        } elseif ($percentage < 40) {
            return 'bg-yellow-400';
        } elseif ($percentage < 60) {
            return 'bg-orange-400';
        } elseif ($percentage < 80) {
            return 'bg-orange-600';
        } else {
            return 'bg-red-600';
        }
    }

    public function getColorHex($count): string
    {
        if ($this->maxCount === 0 || $count === 0) {
            return '#e5e7eb';
        }

        $percentage = ($count / $this->maxCount) * 100;

        if ($percentage < 20) {
            return '#fef08a';
        }
        if ($percentage < 40) {
            return '#facc15';
        }
        if ($percentage < 60) {
            return '#fb923c';
        }
        if ($percentage < 80) {
            return '#ea580c';
        }

        return '#dc2626';
    }

    public function dispatchMapUpdate(): void
    {
        $this->dispatch(
            'regional-map-data-updated',
            payload: $this->mapPayload,
            selectedArea: $this->selectedArea,
            displayMode: $this->displayMode
        );
    }

    public function render()
    {
        return view('livewire.public.regional-distribution-map', [
            'areaData' => $this->areaData,
            'displayMode' => $this->displayMode,
            'colorMode' => $this->colorMode,
            'mapPayload' => $this->mapPayload,
        ]);
    }
}
