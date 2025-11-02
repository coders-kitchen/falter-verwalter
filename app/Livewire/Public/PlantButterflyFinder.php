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
        // If plant IDs are provided via URL, update matching species
        if (!empty($this->selectedPlantIds)) {
            $this->showResults = true;
        }
    }

    public function updatedSelectedPlantIds()
    {
        $this->resetPage();
        if (!empty($this->selectedPlantIds)) {
            $this->showResults = true;
        } else {
            $this->showResults = false;
        }
    }

    public function getMatchingSpeciesQuery()
    {
        $query = Species::with('endangeredRegions', 'generations');

        if (!empty($this->selectedPlantIds)) {
            $query->whereHas('generations', function ($q) {
                $q->where(function ($subQ) {
                    // Check nectar plants
                    foreach ($this->selectedPlantIds as $plantId) {
                        $subQ->orWhereJsonContains('nectar_plants', (int)$plantId);
                    }
                })->orWhere(function ($subQ) {
                    // Check larval host plants
                    foreach ($this->selectedPlantIds as $plantId) {
                        $subQ->orWhereJsonContains('larval_host_plants', (int)$plantId);
                    }
                });
            });
        }

        return $query->distinct()
            ->orderBy('name');
    }

    public function clearSelection()
    {
        $this->reset(['selectedPlantIds', 'showResults']);
        $this->resetPage();
    }

    public function getPlantUseForSpecies($species)
    {
        $uses = [];

        foreach ($this->selectedPlantIds as $plantId) {
            $plantId = (int)$plantId;

            foreach ($species->generations as $generation) {
                // Check nectar plants
                if ($generation->nectar_plants && in_array($plantId, $generation->nectar_plants)) {
                    $uses[] = 'Nektarpflanze';
                }

                // Check larval host plants
                if ($generation->larval_host_plants && in_array($plantId, $generation->larval_host_plants)) {
                    $uses[] = 'Futterpflanze';
                }
            }
        }

        return array_unique($uses);
    }

    public function render()
    {
        $plants = Plant::with('family')
            ->orderBy('name')
            ->get();

        // Get paginated matching species using the query builder
        $paginatedSpecies = $this->getMatchingSpeciesQuery()
            ->paginate(20);

        return view('livewire.public.plant-butterfly-finder', [
            'plants' => $plants,
            'paginatedSpecies' => $paginatedSpecies,
        ]);
    }
}
