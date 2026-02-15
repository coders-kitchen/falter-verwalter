<?php

namespace App\Livewire\Public;

use App\Models\DistributionArea;
use App\Models\Species;
use App\Models\SpeciesDistributionArea;
use Livewire\Component;

class RegionalDistributionMap extends Component
{
    public $species = null;
    public $displayMode = 'all'; // 'all' or 'endangered'
    public $areaData = [];
    public $selectedArea = null;
    public $maxCount = 0;

    public function mount($species = null)
    {
        $this->species = $species;
        $this->aggregateRegionData();
    }

    public function toggleDisplayMode($mode)
    {
        $this->displayMode = $mode;
        $this->aggregateRegionData();
    }

    public function selectArea($areaId)
    {
        $this->selectedArea = $this->selectedArea === $areaId ? null : $areaId;
    }

    public function aggregateRegionData()
    {
        $areas = DistributionArea::orderBy('name')->get();
        $this->areaData = [];
        $this->maxCount = 0;

        foreach ($areas as $area) {
            $query = SpeciesDistributionArea::where('distribution_area_id', $area->id)
                ->when($this->species, function ($q) {
                    $q->where('species_id', $this->species->id);
                });

            if ($this->displayMode === 'endangered') {
                $query->whereHas('threatCategory', function ($q) {
                    $q->where('code', 'VU');
                });
            }

            $count = $query->count();

            $this->areaData[$area->id] = [
                'name' => $area->name,
                'count' => $count,
                'id' => $area->id,
            ];

            if ($count > $this->maxCount) {
                $this->maxCount = $count;
            }
        }
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

    public function render()
    {
        return view('livewire.public.regional-distribution-map', [
            'areaData' => $this->areaData,
            'displayMode' => $this->displayMode,
        ]);
    }
}
