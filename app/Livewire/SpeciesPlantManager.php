<?php

namespace App\Livewire;

use App\Models\Plant;
use App\Models\Species;
use App\Models\SpeciesPlant;
use Livewire\Component;
use Livewire\WithPagination;

class SpeciesPlantManager extends Component
{
    use WithPagination;

    public $species_id;
    public $species;
    public $showModal = false;
    public $speciesPlant = null;

    public $form = [
        'plant_id' => '',
        'is_nectar' => false,
        'is_larval_host' => false,
    ];

    protected $rules = [
        'form.plant_id' => 'required|exists:plants,id',
        'form.is_nectar' => 'boolean',
        'form.is_larval_host' => 'boolean',
    ];

    public function mount($speciesId)
    {
        $this->species_id = $speciesId;
        $this->species = Species::findOrFail($speciesId);
    }

    public function render()
    {
        $speciesPlants = SpeciesPlant::with('plant')
            ->where('species_id', $this->species_id)
            ->orderBy('id', 'desc')
            ->paginate(20);

        $availablePlantsQuery = Plant::orderBy('name');

        if (!$this->speciesPlant) {
            $availablePlantsQuery->whereNotIn(
                'id',
                SpeciesPlant::where('species_id', $this->species_id)->pluck('plant_id')
            );
        }

        return view('livewire.species-plant-manager', [
            'speciesPlants' => $speciesPlants,
            'plants' => $availablePlantsQuery->get(),
        ]);
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(SpeciesPlant $speciesPlant)
    {
        $this->speciesPlant = $speciesPlant;
        $this->form = [
            'plant_id' => $speciesPlant->plant_id,
            'is_nectar' => (bool) $speciesPlant->is_nectar,
            'is_larval_host' => (bool) $speciesPlant->is_larval_host,
        ];
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        if (!$this->form['is_nectar'] && !$this->form['is_larval_host']) {
            $this->addError('form.is_nectar', 'Mindestens eine Nutzung muss ausgewählt sein.');
            return;
        }

        $payload = [
            'plant_id' => (int) $this->form['plant_id'],
            'is_nectar' => (bool) $this->form['is_nectar'],
            'is_larval_host' => (bool) $this->form['is_larval_host'],
        ];

        if ($this->speciesPlant) {
            $this->speciesPlant->update($payload);
            $this->dispatch('notify', message: 'Pflanzenzuordnung aktualisiert');
        } else {
            $alreadyAssigned = SpeciesPlant::where('species_id', $this->species_id)
                ->where('plant_id', $payload['plant_id'])
                ->exists();

            if ($alreadyAssigned) {
                $this->addError('form.plant_id', 'Diese Pflanze ist der Art bereits zugeordnet.');
                return;
            }

            SpeciesPlant::create(array_merge($payload, [
                'species_id' => $this->species_id,
            ]));
            $this->dispatch('notify', message: 'Pflanzenzuordnung erstellt');
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function delete(SpeciesPlant $speciesPlant)
    {
        $speciesPlant->delete();
        $this->resetPage();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->speciesPlant = null;
        $this->form = [
            'plant_id' => '',
            'is_nectar' => false,
            'is_larval_host' => false,
        ];
        $this->resetErrorBag();
    }
}
