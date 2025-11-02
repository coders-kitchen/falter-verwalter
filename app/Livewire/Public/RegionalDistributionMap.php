<?php

namespace App\Livewire\Public;

use App\Models\Region;
use App\Models\Species;
use Livewire\Component;

class RegionalDistributionMap extends Component
{
    public $species = null;
    public $displayMode = 'all'; // 'all' or 'endangered'
    public $regionData = [];
    public $selectedRegion = null;
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

    public function selectRegion($regionCode)
    {
        // For future filtering - can be used to filter species by region
        $this->selectedRegion = $this->selectedRegion === $regionCode ? null : $regionCode;
    }

    public function aggregateRegionData()
    {
        $regions = Region::all();
        $this->regionData = [];
        $this->maxCount = 0;

        foreach ($regions as $region) {
            if ($this->displayMode === 'endangered') {
                // Count endangered species in this region
                $count = $region->species()
                    ->wherePivot('conservation_status', 'gefährdet')
                    ->when($this->species, function ($query) {
                        $query->where('species_region.species_id', $this->species->id);
                    })
                    ->count('species.id');
            } else {
                // Count all species in this region
                $count = $region->species()
                    ->when($this->species, function ($query) {
                        $query->where('species_region.species_id', $this->species->id);
                    })
                    ->count('species.id');
            }

            $this->regionData[$region->code] = [
                'name' => $region->name,
                'code' => $region->code,
                'count' => $count,
                'id' => $region->id,
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
            'regionData' => $this->regionData,
            'displayMode' => $this->displayMode,
        ]);
    }
}
