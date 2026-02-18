<?php

namespace App\Livewire;

use App\Models\Plant;
use App\Models\Species;
use App\Models\SpeciesPlant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class SpeciesPlantManager extends Component
{
    use WithPagination;

    public int $species_id;
    public Species $species;

    public string $assignedSearch = '';
    public string $assignedFilter = 'all';

    public bool $showModal = false;
    public ?SpeciesPlant $speciesPlant = null;

    public array $form = [
        'plant_id' => '',
        'is_nectar' => false,
        'is_larval_host' => false,
    ];

    public string $addSearch = '';
    public array $addSelectedPlantIds = [];

    protected $rules = [
        'form.plant_id' => 'nullable|exists:plants,id',
        'form.is_nectar' => 'boolean',
        'form.is_larval_host' => 'boolean',
    ];

    protected function messages(): array
    {
        return [
            'form.plant_id.exists' => 'Die ausgewählte Pflanze ist ungültig.',
            'form.is_nectar.boolean' => 'Der Wert für Nektarpflanze ist ungültig.',
            'form.is_larval_host.boolean' => 'Der Wert für Futterpflanze ist ungültig.',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'form.plant_id' => 'Pflanze',
            'form.is_nectar' => 'Nektarpflanze',
            'form.is_larval_host' => 'Futterpflanze',
        ];
    }

    public function mount($speciesId): void
    {
        $this->species_id = (int) $speciesId;
        $this->species = Species::findOrFail($speciesId);
    }

    public function updatedAssignedSearch(): void
    {
        $this->resetPage('assignedPage');
    }

    public function updatedAssignedFilter(): void
    {
        $this->resetPage('assignedPage');
    }

    public function updatedAddSearch(): void
    {
        $this->resetPage('addPlantsPage');
    }

    public function updatedAddSelectedPlantIds(): void
    {
        $this->addSelectedPlantIds = array_values(array_unique(array_map('intval', (array) $this->addSelectedPlantIds)));
    }

    public function openCreateModal(): void
    {
        $this->speciesPlant = null;
        $this->form = [
            'plant_id' => '',
            'is_nectar' => false,
            'is_larval_host' => false,
        ];
        $this->addSearch = '';
        $this->addSelectedPlantIds = [];
        $this->resetPage('addPlantsPage');
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function openEditModal(SpeciesPlant $speciesPlant): void
    {
        if ((int) $speciesPlant->species_id !== $this->species_id) {
            return;
        }

        $this->speciesPlant = $speciesPlant;
        $this->form = [
            'plant_id' => (string) $speciesPlant->plant_id,
            'is_nectar' => (bool) $speciesPlant->is_nectar,
            'is_larval_host' => (bool) $speciesPlant->is_larval_host,
        ];
        $this->addSearch = '';
        $this->addSelectedPlantIds = [];
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->speciesPlant = null;
        $this->form = [
            'plant_id' => '',
            'is_nectar' => false,
            'is_larval_host' => false,
        ];
        $this->addSearch = '';
        $this->addSelectedPlantIds = [];
        $this->resetErrorBag();
    }

    public function selectAllOnAddPage(): void
    {
        $page = (int) $this->getPage('addPlantsPage');
        if ($page < 1) {
            $page = 1;
        }

        $ids = $this->buildAddPlantsQuery()
            ->orderBy('name')
            ->forPage($page, 20)
            ->pluck('plants.id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $this->addSelectedPlantIds = array_values(array_unique(array_merge($this->addSelectedPlantIds, $ids)));
    }

    public function clearAddSelection(): void
    {
        $this->addSelectedPlantIds = [];
    }

    public function save(): void
    {
        $this->validate();

        if (!$this->form['is_nectar'] && !$this->form['is_larval_host']) {
            $this->addError('form.is_nectar', 'Mindestens eine Nutzung muss ausgewählt sein.');
            return;
        }

        if ($this->speciesPlant) {
            $this->speciesPlant->update([
                'is_nectar' => (bool) $this->form['is_nectar'],
                'is_larval_host' => (bool) $this->form['is_larval_host'],
            ]);

            $this->dispatch('notify', message: 'Pflanzenzuordnung aktualisiert.');
            $this->closeModal();
            return;
        }

        $plantIds = array_values(array_unique(array_map('intval', $this->addSelectedPlantIds)));
        if (empty($plantIds)) {
            $this->addError('form.plant_id', 'Bitte mindestens eine Pflanze auswählen.');
            return;
        }

        $now = now();
        $rows = [];

        foreach ($plantIds as $plantId) {
            $rows[] = [
                'species_id' => $this->species_id,
                'plant_id' => $plantId,
                'is_nectar' => (bool) $this->form['is_nectar'],
                'is_larval_host' => (bool) $this->form['is_larval_host'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::transaction(function () use ($rows) {
            SpeciesPlant::upsert(
                $rows,
                ['species_id', 'plant_id'],
                ['is_nectar', 'is_larval_host', 'updated_at']
            );
        });

        $this->dispatch('notify', message: count($rows) . ' Pflanzen zugeordnet.');
        $this->closeModal();
        $this->resetPage('assignedPage');
    }

    public function delete(SpeciesPlant $speciesPlant): void
    {
        if ((int) $speciesPlant->species_id !== $this->species_id) {
            return;
        }

        $speciesPlant->delete();
        $this->resetPage('assignedPage');
        $this->dispatch('notify', message: 'Pflanzenzuordnung gelöscht.');
    }

    public function render()
    {
        $speciesPlants = $this->buildAssignedQuery()
            ->with('plant:id,name,scientific_name')
            ->orderByDesc('updated_at')
            ->paginate(20, ['*'], 'assignedPage');

        $addPlants = collect();
        $addPlantsPagination = null;

        if ($this->showModal && !$this->speciesPlant) {
            $addPlantsPagination = $this->buildAddPlantsQuery()
                ->orderBy('name')
                ->paginate(20, ['*'], 'addPlantsPage');
            $addPlants = $addPlantsPagination;
        }

        return view('livewire.species-plant-manager', [
            'speciesPlants' => $speciesPlants,
            'addPlants' => $addPlants,
            'addPlantsPagination' => $addPlantsPagination,
        ]);
    }

    private function buildAssignedQuery(): Builder
    {
        $query = SpeciesPlant::query()->where('species_id', $this->species_id);

        if (trim($this->assignedSearch) !== '') {
            $search = '%' . trim($this->assignedSearch) . '%';
            $query->whereHas('plant', function (Builder $q) use ($search) {
                $q->where(function (Builder $plantQuery) use ($search) {
                    $plantQuery->where('name', 'like', $search)
                        ->orWhere('scientific_name', 'like', $search)
                        ->orWhereHas('genus', function (Builder $genusQuery) use ($search) {
                            $genusQuery->where('name', 'like', $search);
                        });
                });
            });
        }

        if ($this->assignedFilter === 'nectar_only') {
            $query->where('is_nectar', true)->where('is_larval_host', false);
        }

        if ($this->assignedFilter === 'larval_only') {
            $query->where('is_nectar', false)->where('is_larval_host', true);
        }

        if ($this->assignedFilter === 'both') {
            $query->where('is_nectar', true)->where('is_larval_host', true);
        }

        return $query;
    }

    private function buildAddPlantsQuery(): Builder
    {
        $query = Plant::query()->whereNotIn(
            'id',
            SpeciesPlant::where('species_id', $this->species_id)->pluck('plant_id')
        );

        if (trim($this->addSearch) !== '') {
            $search = '%' . trim($this->addSearch) . '%';
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', $search)
                    ->orWhere('scientific_name', 'like', $search)
                    ->orWhereHas('genus', function (Builder $genusQuery) use ($search) {
                        $genusQuery->where('name', 'like', $search);
                    });
            });
        }

        return $query;
    }
}
