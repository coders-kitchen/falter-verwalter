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
            'genus.subfamily.family',
            'genus.tribe',
            'habitats',
            'generations' => function ($query) {
                $query->orderBy('generation_number');
            },
            'distributionAreas' => function ($query) {
                $query->orderBy('threat_category_id');
            },
            'primaryNectarPlants' => function ($query) {
                $query->orderBy('name');
            },
            'secondaryNectarPlants' => function ($query) {
                $query->orderBy('name');
            },
            'primaryLarvalHostPlants' => function ($query) {
                $query->orderBy('name');
            },
            'secondaryLarvalHostPlants' => function ($query) {
                $query->orderBy('name');
            },
        ]);
    }

    public function render()
    {
        return view('livewire.public.species-detail', [
            'species' => $this->species,
        ]);
    }
}
