<?php

namespace App\Livewire\Public;

use App\Models\Plant;
use App\Models\Species;
use Livewire\Component;
use Livewire\WithPagination;

class PlantButterflyFinder extends Component
{
    use WithPagination;

    public $selectedPlantIds = [];
    public $showResults = false;

    protected $queryString = ['selectedPlantIds', 'page'];

    public function mount()
    {
        if (!empty($this->selectedPlantIds)) {
            $this->showResults = true;
        }
    }

    public function updatedSelectedPlantIds()
    {
        $this->selectedPlantIds = array_map('intval', (array) $this->selectedPlantIds);
        $this->resetPage();
        $this->showResults = !empty($this->selectedPlantIds);
    }

    public function getMatchingSpeciesQuery()
    {
        $query = Species::with(['plants', 'distributionAreas']);

        if (!empty($this->selectedPlantIds)) {
            $query->whereHas('plants', function ($q) {
                $q->whereIn('plants.id', $this->selectedPlantIds)
                    ->where(function ($pivotQuery) {
                        $pivotQuery->where('species_plant.is_nectar', true)
                            ->orWhere('species_plant.is_larval_host', true);
                    });
            });
        }

        return $query->distinct()->orderBy('name');
    }

    public function clearSelection()
    {
        $this->reset(['selectedPlantIds', 'showResults']);
        $this->resetPage();
    }

    public function removeSelectedPlant($plantId)
    {
        $position = array_search((int) $plantId, array_map('intval', $this->selectedPlantIds), true);

        if ($position !== false) {
            array_splice($this->selectedPlantIds, $position, 1);
        }

        $this->selectedPlantIds = array_values(array_map('intval', $this->selectedPlantIds));
        $this->showResults = !empty($this->selectedPlantIds);
    }

    public function getPlantUseForSpecies($species)
    {
        $uses = [];

        foreach ($species->plants as $plant) {
            if (!in_array((int) $plant->id, array_map('intval', $this->selectedPlantIds), true)) {
                continue;
            }

            if ($plant->pivot->is_nectar) {
                $uses[] = 'Nektarpflanze';
            }

            if ($plant->pivot->is_larval_host) {
                $uses[] = 'Futterpflanze';
            }
        }

        return array_values(array_unique($uses));
    }

    public function render()
    {
        $plants = Plant::with('family')
            ->orderBy('name')
            ->get();

        $paginatedSpecies = $this->getMatchingSpeciesQuery()->paginate(20);

        return view('livewire.public.plant-butterfly-finder', [
            'plants' => $plants,
            'paginatedSpecies' => $paginatedSpecies,
        ]);
    }
}
