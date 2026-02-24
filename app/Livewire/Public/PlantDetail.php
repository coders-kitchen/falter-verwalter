<?php

namespace App\Livewire\Public;

use App\Models\Plant;
use App\Models\Species;
use App\Models\SpeciesPlant;
use Livewire\Component;

class PlantDetail extends Component
{
    public Plant $plant;

    public function mount(Plant $plant)
    {
        $this->plant = $plant->load([
            'family',
            'habitats',
            'threatCategory',
        ]);
    }

    public function render()
    {
        $nectarSpecies = Species::whereHas('plants', function ($query) {
            $query->where('plants.id', $this->plant->id)
                ->where('species_plant.is_nectar', true)
                ->where('species_plant.adult_preference', SpeciesPlant::PREFERENCE_PRIMARY);
        })
            ->orderBy('name')
            ->get();

        $larvalSpecies = Species::whereHas('plants', function ($query) {
            $query->where('plants.id', $this->plant->id)
                ->where('species_plant.is_larval_host', true)
                ->where('species_plant.larval_preference', SpeciesPlant::PREFERENCE_PRIMARY);
        })
            ->orderBy('name')
            ->get();

        return view('livewire.public.plant-detail', [
            'plant' => $this->plant,
            'nectarSpecies' => $nectarSpecies,
            'larvalSpecies' => $larvalSpecies,
        ]);
    }
}
