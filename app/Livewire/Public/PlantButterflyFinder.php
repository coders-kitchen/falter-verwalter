<?php

namespace App\Livewire\Public;

use App\Models\Plant;
use App\Models\Species;
use App\Models\SpeciesPlant;
use Livewire\Component;
use Livewire\WithPagination;

class PlantButterflyFinder extends Component
{
    use WithPagination;

    public $selectedPlantIds = [];
    public $showResults = false;
    public $plantSearch = '';
    public $filterBloomMonth = null;
    public $filterHeightMin = null;
    public $filterHeightMax = null;
    public $filterLight = '';
    public $filterSalt = '';
    public $filterTemperature = '';
    public $filterContinentality = '';
    public $filterReaction = '';
    public $filterMoisture = '';
    public $filterMoistureVariation = '';
    public $filterNitrogen = '';

    protected $queryString = [
        'selectedPlantIds',
        'plantSearch',
        'filterBloomMonth',
        'filterHeightMin',
        'filterHeightMax',
        'filterLight',
        'filterSalt',
        'filterTemperature',
        'filterContinentality',
        'filterReaction',
        'filterMoisture',
        'filterMoistureVariation',
        'filterNitrogen',
        'page',
    ];

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

    public function updatedPlantSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterBloomMonth(): void
    {
        $this->resetPage();
    }

    public function updatedFilterHeightMin(): void
    {
        $this->resetPage();
    }

    public function updatedFilterHeightMax(): void
    {
        $this->resetPage();
    }

    public function updatedFilterLight(): void
    {
        $this->resetPage();
    }

    public function updatedFilterSalt(): void
    {
        $this->resetPage();
    }

    public function updatedFilterTemperature(): void
    {
        $this->resetPage();
    }

    public function updatedFilterContinentality(): void
    {
        $this->resetPage();
    }

    public function updatedFilterReaction(): void
    {
        $this->resetPage();
    }

    public function updatedFilterMoisture(): void
    {
        $this->resetPage();
    }

    public function updatedFilterMoistureVariation(): void
    {
        $this->resetPage();
    }

    public function updatedFilterNitrogen(): void
    {
        $this->resetPage();
    }

    public function getMatchingSpeciesQuery()
    {
        $query = Species::with(['plants', 'distributionAreas']);

        if (!empty($this->selectedPlantIds)) {
            $query->whereHas('plants', function ($q) {
                $q->whereIn('plants.id', $this->selectedPlantIds)
                    ->where(function ($pivotQuery) {
                        $pivotQuery->where(function ($adultQuery) {
                            $adultQuery->where('species_plant.is_nectar', true)
                                ->where('species_plant.adult_preference', SpeciesPlant::PREFERENCE_PRIMARY);
                        })->orWhere(function ($larvalQuery) {
                            $larvalQuery->where('species_plant.is_larval_host', true)
                                ->where('species_plant.larval_preference', SpeciesPlant::PREFERENCE_PRIMARY);
                        });
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

    public function resetPlantFilters(): void
    {
        $this->reset([
            'plantSearch',
            'filterBloomMonth',
            'filterHeightMin',
            'filterHeightMax',
            'filterLight',
            'filterSalt',
            'filterTemperature',
            'filterContinentality',
            'filterReaction',
            'filterMoisture',
            'filterMoistureVariation',
            'filterNitrogen',
        ]);
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

            if ($plant->pivot->is_nectar && $plant->pivot->adult_preference === SpeciesPlant::PREFERENCE_PRIMARY) {
                $uses[] = 'Nektarpflanze';
            }

            if ($plant->pivot->is_larval_host && $plant->pivot->larval_preference === SpeciesPlant::PREFERENCE_PRIMARY) {
                $uses[] = 'Futterpflanze';
            }
        }

        return array_values(array_unique($uses));
    }

    private function applyIndicatorFilter($query, string $column, string $stateColumn, mixed $filterValue): void
    {
        if ($filterValue === '' || $filterValue === null) {
            return;
        }

        if ($filterValue === 'x') {
            $query->where($stateColumn, 'x');
            return;
        }

        if ($filterValue === 'unknown') {
            $query->where($stateColumn, 'unknown');
            return;
        }

        if (is_numeric($filterValue)) {
            $query->where($stateColumn, 'numeric')
                ->where($column, (int) $filterValue);
        }
    }

    private function getIndicatorOptions(int $max, int $min = 1): array
    {
        $options = [
            ['value' => '', 'label' => 'Alle'],
            ['value' => 'x', 'label' => 'X (indifferent)'],
            ['value' => 'unknown', 'label' => '? (ungeklärt)'],
        ];

        for ($i = $min; $i <= $max; $i++) {
            $options[] = ['value' => (string) $i, 'label' => (string) $i];
        }

        return $options;
    }

    public function render()
    {
        $plantQuery = Plant::query()
            ->with('family')
            ->orderBy('name');

        if ($this->plantSearch !== '') {
            $search = trim($this->plantSearch);
            $plantQuery->where('name', 'like', "%{$search}%");
        }

        if ($this->filterBloomMonth) {
            $month = (int) $this->filterBloomMonth;
            $plantQuery->whereNotNull('bloom_start_month')
                ->whereNotNull('bloom_end_month')
                ->where(function ($q) use ($month) {
                    $q->where(function ($sq) use ($month) {
                        $sq->whereColumn('bloom_start_month', '<=', 'bloom_end_month')
                            ->where('bloom_start_month', '<=', $month)
                            ->where('bloom_end_month', '>=', $month);
                    })->orWhere(function ($sq) use ($month) {
                        $sq->whereColumn('bloom_start_month', '>', 'bloom_end_month')
                            ->where(function ($wrap) use ($month) {
                                $wrap->where('bloom_start_month', '<=', $month)
                                    ->orWhere('bloom_end_month', '>=', $month);
                            });
                    });
                });
        }

        if ($this->filterHeightMin !== null && $this->filterHeightMin !== '') {
            $plantQuery->where(function ($q) {
                $q->whereNull('plant_height_cm_until')
                    ->orWhere('plant_height_cm_until', '>=', (int) $this->filterHeightMin);
            });
        }

        if ($this->filterHeightMax !== null && $this->filterHeightMax !== '') {
            $plantQuery->where(function ($q) {
                $q->whereNull('plant_height_cm_from')
                    ->orWhere('plant_height_cm_from', '<=', (int) $this->filterHeightMax);
            });
        }

        $this->applyIndicatorFilter($plantQuery, 'light_number', 'light_number_state', $this->filterLight);
        $this->applyIndicatorFilter($plantQuery, 'salt_number', 'salt_number_state', $this->filterSalt);
        $this->applyIndicatorFilter($plantQuery, 'temperature_number', 'temperature_number_state', $this->filterTemperature);
        $this->applyIndicatorFilter($plantQuery, 'continentality_number', 'continentality_number_state', $this->filterContinentality);
        $this->applyIndicatorFilter($plantQuery, 'reaction_number', 'reaction_number_state', $this->filterReaction);
        $this->applyIndicatorFilter($plantQuery, 'moisture_number', 'moisture_number_state', $this->filterMoisture);
        $this->applyIndicatorFilter($plantQuery, 'moisture_variation', 'moisture_variation_state', $this->filterMoistureVariation);
        $this->applyIndicatorFilter($plantQuery, 'nitrogen_number', 'nitrogen_number_state', $this->filterNitrogen);

        $plants = $plantQuery->get();

        $paginatedSpecies = $this->getMatchingSpeciesQuery()->paginate(20);

        $selectedPlants = Plant::query()
            ->whereIn('id', array_map('intval', $this->selectedPlantIds))
            ->orderBy('name')
            ->get()
            ->keyBy('id');

        return view('livewire.public.plant-butterfly-finder', [
            'plants' => $plants,
            'selectedPlants' => $selectedPlants,
            'paginatedSpecies' => $paginatedSpecies,
            'monthOptions' => [
                1 => 'Januar',
                2 => 'Februar',
                3 => 'März',
                4 => 'April',
                5 => 'Mai',
                6 => 'Juni',
                7 => 'Juli',
                8 => 'August',
                9 => 'September',
                10 => 'Oktober',
                11 => 'November',
                12 => 'Dezember',
            ],
            'indicatorOptions' => [
                'light' => $this->getIndicatorOptions(9),
                'salt' => $this->getIndicatorOptions(9, 0),
                'temperature' => $this->getIndicatorOptions(9),
                'continentality' => $this->getIndicatorOptions(9),
                'reaction' => $this->getIndicatorOptions(9),
                'moisture' => $this->getIndicatorOptions(12),
                'moistureVariation' => $this->getIndicatorOptions(9),
                'nitrogen' => $this->getIndicatorOptions(9),
            ],
        ]);
    }
}
