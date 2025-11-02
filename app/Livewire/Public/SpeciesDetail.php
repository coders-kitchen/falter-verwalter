<?php

namespace App\Livewire\Public;

use App\Models\Species;
use Livewire\Component;

class SpeciesDetail extends Component
{
    public Species $species;

    public function mount(Species $species)
    {
        $this->species = $species->load([
            'family',
            'habitats',
            'endangeredRegions',
            'generations' => function ($query) {
                $query->orderBy('generation_number');
            }
        ]);

        // Load plants for each generation
        foreach ($this->species->generations as $generation) {
            $generation->plants = $generation->plants();
        }
    }

    public function render()
    {
        return view('livewire.public.species-detail', [
            'species' => $this->species,
        ]);
    }
}
