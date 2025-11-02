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
            'regions' => function ($query) {
                $query->orderBy('code');
            },
            'endangeredRegionsList',
            'generations' => function ($query) {
                $query->orderBy('generation_number');
            }
        ]);
    }

    public function render()
    {
        return view('livewire.public.species-detail', [
            'species' => $this->species,
        ]);
    }
}
