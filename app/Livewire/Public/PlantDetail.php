<?php

namespace App\Livewire\Public;

use App\Models\Plant;
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
        // Get species that use this plant as nectar plant
        $nectarSpecies = \App\Models\Species::with('generations')
            ->whereHas('generations', function ($query) {
                $query->whereJsonContains('nectar_plants', $this->plant->id);
            })
            ->orderBy('name')
            ->get();

        // Get species that use this plant as larval host plant
        $larvalSpecies = \App\Models\Species::with('generations')
            ->whereHas('generations', function ($query) {
                $query->whereJsonContains('larval_host_plants', $this->plant->id);
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
