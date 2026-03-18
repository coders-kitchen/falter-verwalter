<?php

namespace App\Livewire\Public;

use App\Models\DistributionArea;
use App\Models\SpeciesDistributionArea;
use Livewire\Component;

class RegionalDistributionMap extends Component
{
    public $species = null;
    public $displayMode = 'all'; // 'all' or 'endangered'
    public $colorMode = 'count'; // 'count' or 'threat'
    public $areaData = [];
    public $maxCount = 0;

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

    public function aggregateRegionData()
    {
        $areas = DistributionArea::query()
            ->with('level:id,name,code,sort_order,map_role')
            ->select(['id', 'distribution_area_level_id', 'name', 'code', 'geojson_path'])
            ->get()
            ->sortBy(function (DistributionArea $area) {
                return sprintf('%05d-%s', $area->level?->sort_order ?? 99999, mb_strtolower($area->name));
            })
            ->values();
        $countsByAreaId = $this->loadCountsByAreaId();
        $threatByAreaId = $this->loadThreatByAreaId();

        $this->areaData = [];
        $this->maxCount = 0;

        foreach ($areas as $area) {
            $count = (int) ($countsByAreaId[$area->id] ?? 0);
            $threat = $threatByAreaId[$area->id] ?? null;
            $featureColor = $this->resolveFeatureColor($count, $threat);

            $this->areaData[] = [
                'name' => $area->name,
                'count' => $count,
                'id' => $area->id,
                'code' => $area->code,
                'geometry_available' => !empty($area->geojson_path),
                'level_name' => $area->level?->name,
                'level_code' => $area->level?->code,
                'level_sort_order' => $area->level?->sort_order,
                'level_map_role' => $area->level?->map_role ?? 'detail',
                'threat_code' => $threat['code'] ?? null,
                'threat_label' => $threat['label'] ?? null,
                'threat_color' => $threat['color_code'] ?? null,
                'fill_color' => $featureColor,
            ];

            if ($count > $this->maxCount) {
                $this->maxCount = $count;
            }
        }
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
            areaData: $this->areaData,
            displayMode: $this->displayMode,
            colorMode: $this->colorMode,
            speciesId: $this->species?->id
        );
    }

    public function render()
    {
        return view('livewire.public.regional-distribution-map', [
            'areaData' => $this->areaData,
            'displayMode' => $this->displayMode,
            'colorMode' => $this->colorMode,
            'speciesId' => $this->species?->id,
        ]);
    }
}
