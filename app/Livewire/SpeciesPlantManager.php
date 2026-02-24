<?php

namespace App\Livewire;

use App\Models\Genus;
use App\Models\Plant;
use App\Models\Species;
use App\Models\SpeciesGenus;
use App\Models\SpeciesPlant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
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
    public ?SpeciesGenus $speciesGenus = null;
    public string $assignmentType = 'plant';

    public array $form = [
        'is_nectar' => false,
        'is_larval_host' => false,
        'adult_preference' => null,
        'larval_preference' => null,
    ];

    public string $addSearch = '';
    public array $addSelectedPlantIds = [];
    public array $addSelectedGenusIds = [];

    protected $rules = [
        'form.is_nectar' => 'boolean',
        'form.is_larval_host' => 'boolean',
        'form.adult_preference' => 'nullable|in:primaer,sekundaer',
        'form.larval_preference' => 'nullable|in:primaer,sekundaer',
        'assignmentType' => 'in:plant,genus',
    ];

    protected function messages(): array
    {
        return [
            'form.is_nectar.boolean' => 'Der Wert für Nektarpflanze ist ungültig.',
            'form.is_larval_host.boolean' => 'Der Wert für Futterpflanze ist ungültig.',
            'form.adult_preference.in' => 'Die Präferenz für adulte Falter ist ungültig.',
            'form.larval_preference.in' => 'Die Präferenz für Raupen ist ungültig.',
            'assignmentType.in' => 'Der Zuordnungstyp ist ungültig.',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'form.is_nectar' => 'Nektarpflanze',
            'form.is_larval_host' => 'Futterpflanze',
            'form.adult_preference' => 'Präferenz (Adulte)',
            'form.larval_preference' => 'Präferenz (Raupe)',
            'assignmentType' => 'Zuordnungstyp',
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
        $this->resetPage('addItemsPage');
    }

    public function updatedAssignmentType(): void
    {
        if ($this->speciesPlant || $this->speciesGenus) {
            return;
        }

        $this->addSearch = '';
        $this->addSelectedPlantIds = [];
        $this->addSelectedGenusIds = [];
        $this->resetPage('addItemsPage');
    }

    public function updatedAddSelectedPlantIds(): void
    {
        $this->addSelectedPlantIds = array_values(array_unique(array_map('intval', (array) $this->addSelectedPlantIds)));
    }

    public function updatedAddSelectedGenusIds(): void
    {
        $this->addSelectedGenusIds = array_values(array_unique(array_map('intval', (array) $this->addSelectedGenusIds)));
    }

    public function openCreateModal(): void
    {
        $this->speciesPlant = null;
        $this->speciesGenus = null;
        $this->assignmentType = 'plant';
        $this->form = [
            'is_nectar' => false,
            'is_larval_host' => false,
            'adult_preference' => null,
            'larval_preference' => null,
        ];
        $this->addSearch = '';
        $this->addSelectedPlantIds = [];
        $this->addSelectedGenusIds = [];
        $this->resetPage('addItemsPage');
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function openEditPlantModal(SpeciesPlant $speciesPlant): void
    {
        if ((int) $speciesPlant->species_id !== $this->species_id) {
            return;
        }

        $this->speciesPlant = $speciesPlant;
        $this->speciesGenus = null;
        $this->assignmentType = 'plant';
        $this->form = [
            'is_nectar' => (bool) $speciesPlant->is_nectar,
            'is_larval_host' => (bool) $speciesPlant->is_larval_host,
            'adult_preference' => $speciesPlant->is_nectar
                ? ($speciesPlant->adult_preference ?? SpeciesPlant::PREFERENCE_PRIMARY)
                : null,
            'larval_preference' => $speciesPlant->is_larval_host
                ? ($speciesPlant->larval_preference ?? SpeciesPlant::PREFERENCE_PRIMARY)
                : null,
        ];
        $this->addSearch = '';
        $this->addSelectedPlantIds = [];
        $this->addSelectedGenusIds = [];
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function openEditGenusModal(SpeciesGenus $speciesGenus): void
    {
        if ((int) $speciesGenus->species_id !== $this->species_id) {
            return;
        }

        $this->speciesGenus = $speciesGenus;
        $this->speciesPlant = null;
        $this->assignmentType = 'genus';
        $this->form = [
            'is_nectar' => (bool) $speciesGenus->is_nectar,
            'is_larval_host' => (bool) $speciesGenus->is_larval_host,
            'adult_preference' => $speciesGenus->is_nectar
                ? ($speciesGenus->adult_preference ?? SpeciesPlant::PREFERENCE_PRIMARY)
                : null,
            'larval_preference' => $speciesGenus->is_larval_host
                ? ($speciesGenus->larval_preference ?? SpeciesPlant::PREFERENCE_PRIMARY)
                : null,
        ];
        $this->addSearch = '';
        $this->addSelectedPlantIds = [];
        $this->addSelectedGenusIds = [];
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->speciesPlant = null;
        $this->speciesGenus = null;
        $this->assignmentType = 'plant';
        $this->form = [
            'is_nectar' => false,
            'is_larval_host' => false,
            'adult_preference' => null,
            'larval_preference' => null,
        ];
        $this->addSearch = '';
        $this->addSelectedPlantIds = [];
        $this->addSelectedGenusIds = [];
        $this->resetErrorBag();
    }

    public function selectAllOnAddPage(): void
    {
        $page = max(1, (int) $this->getPage('addItemsPage'));

        if ($this->assignmentType === 'genus') {
            $ids = $this->buildAddGeneraQuery()
                ->orderBy('name')
                ->forPage($page, 20)
                ->pluck('genera.id')
                ->map(fn ($id) => (int) $id)
                ->all();

            $this->addSelectedGenusIds = array_values(array_unique(array_merge($this->addSelectedGenusIds, $ids)));

            return;
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
        if ($this->assignmentType === 'genus') {
            $this->addSelectedGenusIds = [];
            return;
        }

        $this->addSelectedPlantIds = [];
    }

    public function updatedFormIsNectar($value): void
    {
        if (!(bool) $value) {
            $this->form['adult_preference'] = null;
            return;
        }

        if ($this->form['adult_preference'] === null || $this->form['adult_preference'] === '') {
            $this->form['adult_preference'] = SpeciesPlant::PREFERENCE_PRIMARY;
        }
    }

    public function updatedFormIsLarvalHost($value): void
    {
        if (!(bool) $value) {
            $this->form['larval_preference'] = null;
            return;
        }

        if ($this->form['larval_preference'] === null || $this->form['larval_preference'] === '') {
            $this->form['larval_preference'] = SpeciesPlant::PREFERENCE_PRIMARY;
        }
    }

    public function save(): void
    {
        $this->validate();

        if (!$this->form['is_nectar'] && !$this->form['is_larval_host']) {
            $this->addError('form.is_nectar', 'Mindestens eine Nutzung muss ausgewählt sein.');
            return;
        }

        $adultPreference = (bool) $this->form['is_nectar']
            ? ($this->normalizePreference($this->form['adult_preference']) ?? SpeciesPlant::PREFERENCE_PRIMARY)
            : null;

        $larvalPreference = (bool) $this->form['is_larval_host']
            ? ($this->normalizePreference($this->form['larval_preference']) ?? SpeciesPlant::PREFERENCE_PRIMARY)
            : null;

        if ($this->speciesPlant) {
            $this->speciesPlant->update([
                'is_nectar' => (bool) $this->form['is_nectar'],
                'is_larval_host' => (bool) $this->form['is_larval_host'],
                'adult_preference' => $adultPreference,
                'larval_preference' => $larvalPreference,
            ]);

            $this->dispatch('notify', message: 'Pflanzenzuordnung aktualisiert.');
            $this->closeModal();
            return;
        }

        if ($this->speciesGenus) {
            $this->speciesGenus->update([
                'is_nectar' => (bool) $this->form['is_nectar'],
                'is_larval_host' => (bool) $this->form['is_larval_host'],
                'adult_preference' => $adultPreference,
                'larval_preference' => $larvalPreference,
            ]);

            $this->dispatch('notify', message: 'Gattungszuordnung aktualisiert.');
            $this->closeModal();
            return;
        }

        $now = now();

        if ($this->assignmentType === 'genus') {
            $genusIds = array_values(array_unique(array_map('intval', $this->addSelectedGenusIds)));

            if (empty($genusIds)) {
                $this->addError('selection', 'Bitte mindestens eine Gattung auswählen.');
                return;
            }

            $rows = [];
            foreach ($genusIds as $genusId) {
                $rows[] = [
                    'species_id' => $this->species_id,
                    'genus_id' => $genusId,
                    'is_nectar' => (bool) $this->form['is_nectar'],
                    'is_larval_host' => (bool) $this->form['is_larval_host'],
                    'adult_preference' => $adultPreference,
                    'larval_preference' => $larvalPreference,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::transaction(function () use ($rows) {
                SpeciesGenus::upsert(
                    $rows,
                    ['species_id', 'genus_id'],
                    ['is_nectar', 'is_larval_host', 'adult_preference', 'larval_preference', 'updated_at']
                );
            });

            $this->dispatch('notify', message: count($rows) . ' Gattungen zugeordnet.');
            $this->closeModal();
            $this->resetPage('assignedPage');
            return;
        }

        $plantIds = array_values(array_unique(array_map('intval', $this->addSelectedPlantIds)));

        if (empty($plantIds)) {
            $this->addError('selection', 'Bitte mindestens eine Pflanze auswählen.');
            return;
        }

        $rows = [];
        foreach ($plantIds as $plantId) {
            $rows[] = [
                'species_id' => $this->species_id,
                'plant_id' => $plantId,
                'is_nectar' => (bool) $this->form['is_nectar'],
                'is_larval_host' => (bool) $this->form['is_larval_host'],
                'adult_preference' => $adultPreference,
                'larval_preference' => $larvalPreference,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::transaction(function () use ($rows) {
            SpeciesPlant::upsert(
                $rows,
                ['species_id', 'plant_id'],
                ['is_nectar', 'is_larval_host', 'adult_preference', 'larval_preference', 'updated_at']
            );
        });

        $this->dispatch('notify', message: count($rows) . ' Pflanzen zugeordnet.');
        $this->closeModal();
        $this->resetPage('assignedPage');
    }

    public function deletePlant(SpeciesPlant $speciesPlant): void
    {
        if ((int) $speciesPlant->species_id !== $this->species_id) {
            return;
        }

        $speciesPlant->delete();
        $this->resetPage('assignedPage');
        $this->dispatch('notify', message: 'Pflanzenzuordnung gelöscht.');
    }

    public function deleteGenus(SpeciesGenus $speciesGenus): void
    {
        if ((int) $speciesGenus->species_id !== $this->species_id) {
            return;
        }

        $speciesGenus->delete();
        $this->resetPage('assignedPage');
        $this->dispatch('notify', message: 'Gattungszuordnung gelöscht.');
    }

    public function render()
    {
        $speciesAssignments = $this->buildAssignedRowsPaginator();

        $addItems = collect();
        $addItemsPagination = null;

        if ($this->showModal && !$this->speciesPlant && !$this->speciesGenus) {
            if ($this->assignmentType === 'genus') {
                $addItemsPagination = $this->buildAddGeneraQuery()
                    ->with(['subfamily.family', 'tribe'])
                    ->orderBy('name')
                    ->paginate(20, ['*'], 'addItemsPage');
            } else {
                $addItemsPagination = $this->buildAddPlantsQuery()
                    ->orderBy('name')
                    ->paginate(20, ['*'], 'addItemsPage');
            }

            $addItems = $addItemsPagination;
        }

        return view('livewire.species-plant-manager', [
            'speciesAssignments' => $speciesAssignments,
            'addItems' => $addItems,
            'addItemsPagination' => $addItemsPagination,
        ]);
    }

    private function buildAssignedRowsPaginator(): LengthAwarePaginator
    {
        $page = max(1, (int) $this->getPage('assignedPage'));
        $perPage = 20;

        $plantRows = $this->buildAssignedPlantQuery()
            ->with('plant:id,name,scientific_name')
            ->get()
            ->map(function (SpeciesPlant $row) {
                return [
                    'type' => 'plant',
                    'id' => $row->id,
                    'name' => $row->plant?->name ?? '—',
                    'subtitle' => $row->plant?->scientific_name,
                    'is_nectar' => (bool) $row->is_nectar,
                    'is_larval_host' => (bool) $row->is_larval_host,
                    'adult_preference' => $row->adult_preference,
                    'larval_preference' => $row->larval_preference,
                    'updated_at' => $row->updated_at,
                ];
            });

        $genusRows = $this->buildAssignedGenusQuery()
            ->with(['genus.subfamily.family', 'genus.tribe'])
            ->get()
            ->map(function (SpeciesGenus $row) {
                return [
                    'type' => 'genus',
                    'id' => $row->id,
                    'name' => ($row->genus?->name ?? '—') . ' (sp.)',
                    'subtitle' => $row->genus?->displayLabel(),
                    'is_nectar' => (bool) $row->is_nectar,
                    'is_larval_host' => (bool) $row->is_larval_host,
                    'adult_preference' => $row->adult_preference,
                    'larval_preference' => $row->larval_preference,
                    'updated_at' => $row->updated_at,
                ];
            });

        $allRows = $plantRows
            ->merge($genusRows)
            ->sortByDesc(function (array $row) {
                return $row['updated_at']?->getTimestamp() ?? 0;
            })
            ->values();

        $total = $allRows->count();

        $items = $allRows->slice(($page - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'pageName' => 'assignedPage',
            ]
        );
    }

    private function buildAssignedPlantQuery(): Builder
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

        return $this->applyAssignedFilter($query);
    }

    private function buildAssignedGenusQuery(): Builder
    {
        $query = SpeciesGenus::query()->where('species_id', $this->species_id);

        if (trim($this->assignedSearch) !== '') {
            $search = '%' . trim($this->assignedSearch) . '%';
            $query->whereHas('genus', function (Builder $q) use ($search) {
                $q->where('name', 'like', $search)
                    ->orWhereHas('subfamily', function (Builder $subfamilyQuery) use ($search) {
                        $subfamilyQuery->where('name', 'like', $search)
                            ->orWhereHas('family', function (Builder $familyQuery) use ($search) {
                                $familyQuery->where('name', 'like', $search);
                            });
                    })
                    ->orWhereHas('tribe', function (Builder $tribeQuery) use ($search) {
                        $tribeQuery->where('name', 'like', $search);
                    });
            });
        }

        return $this->applyAssignedFilter($query);
    }

    private function applyAssignedFilter(Builder $query): Builder
    {
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

    private function buildAddGeneraQuery(): Builder
    {
        $query = Genus::query()
            ->whereHas('subfamily.family', function (Builder $familyQuery) {
                $familyQuery->where('type', 'plant');
            })
            ->whereNotIn(
                'id',
                SpeciesGenus::where('species_id', $this->species_id)->pluck('genus_id')
            );

        if (trim($this->addSearch) !== '') {
            $search = '%' . trim($this->addSearch) . '%';
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', $search)
                    ->orWhereHas('subfamily', function (Builder $subfamilyQuery) use ($search) {
                        $subfamilyQuery->where('name', 'like', $search)
                            ->orWhereHas('family', function (Builder $familyQuery) use ($search) {
                                $familyQuery->where('name', 'like', $search);
                            });
                    })
                    ->orWhereHas('tribe', function (Builder $tribeQuery) use ($search) {
                        $tribeQuery->where('name', 'like', $search);
                    });
            });
        }

        return $query;
    }

    private function normalizePreference(mixed $value): ?string
    {
        if ($value === '' || $value === null) {
            return null;
        }

        return in_array($value, [SpeciesPlant::PREFERENCE_PRIMARY, SpeciesPlant::PREFERENCE_SECONDARY], true)
            ? (string) $value
            : null;
    }
}
