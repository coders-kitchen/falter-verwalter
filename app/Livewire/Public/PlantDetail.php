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
        $directNectarSpecies = Species::whereHas('plants', function ($query) {
            $query->where('plants.id', $this->plant->id)
                ->where('species_plant.is_nectar', true)
                ->where('species_plant.adult_preference', SpeciesPlant::PREFERENCE_PRIMARY);
        })
            ->orderBy('name')
            ->get();

        $directLarvalSpecies = Species::whereHas('plants', function ($query) {
            $query->where('plants.id', $this->plant->id)
                ->where('species_plant.is_larval_host', true)
                ->where('species_plant.larval_preference', SpeciesPlant::PREFERENCE_PRIMARY);
        })
            ->orderBy('name')
            ->get();

        $genusNectarSpecies = collect();
        $genusLarvalSpecies = collect();

        if ($this->plant->genus_id) {
            $genusNectarSpecies = Species::whereHas('plantGenera', function ($query) {
                $query->where('genera.id', $this->plant->genus_id)
                    ->where('species_genus.is_nectar', true)
                    ->where('species_genus.adult_preference', SpeciesPlant::PREFERENCE_PRIMARY);
            })
                ->orderBy('name')
                ->get();

            $genusLarvalSpecies = Species::whereHas('plantGenera', function ($query) {
                $query->where('genera.id', $this->plant->genus_id)
                    ->where('species_genus.is_larval_host', true)
                    ->where('species_genus.larval_preference', SpeciesPlant::PREFERENCE_PRIMARY);
            })
                ->orderBy('name')
                ->get();
        }

        $nectarSpecies = $this->mergeDirectAndGenusMatches($directNectarSpecies, $genusNectarSpecies);
        $larvalSpecies = $this->mergeDirectAndGenusMatches($directLarvalSpecies, $genusLarvalSpecies);

        return view('livewire.public.plant-detail', [
            'plant' => $this->plant,
            'nectarSpecies' => $nectarSpecies,
            'larvalSpecies' => $larvalSpecies,
        ]);
    }

    private function mergeDirectAndGenusMatches($directSpecies, $genusSpecies)
    {
        $rows = collect();

        foreach ($directSpecies as $species) {
            $rows->put((int) $species->id, [
                'species' => $species,
                'via_genus' => false,
            ]);
        }

        foreach ($genusSpecies as $species) {
            if (!$rows->has((int) $species->id)) {
                $rows->put((int) $species->id, [
                    'species' => $species,
                    'via_genus' => true,
                ]);
            }
        }

        return $rows->sortBy(fn (array $row) => $row['species']->name)->values();
    }
}
