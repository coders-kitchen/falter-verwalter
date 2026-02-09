<?php

namespace App\Livewire;

use App\Models\DistributionArea;
use App\Models\SpeciesDistributionArea;
use App\Models\Species;
use App\Models\ThreatCategory;
use Livewire\Component;
use Livewire\WithPagination;

class SpeciesDistributionAreaManager extends Component
{
    use WithPagination;

    public $species_id;
    public $species;
    public $species_distribution_areas = null;
    public $showModal = false;
    public $speciesArea  = null;
    public $search = '';

    public $form = [
        'distribution_area_id' => '',
        'status' => 'heimisch',
        'thread_category_id' => ''
    ];

    protected $rules = [
        'form.distribution_area_id' => 'required|exists:distribution_areas,id',
        'form.status' => 'required|in:heimisch,ausgestorben,neobiotisch',
        'form.thread_category_id' => 'exists:threat_categories,id',
    ];

    public function mount($speciesId)
    {
        $this->species_id = $speciesId;
        $this->species = Species::findOrFail($speciesId);
    }

    public function render()
    {

        $species_distribution_areas = SpeciesDistributionArea::where('species_id', $this->species_id)
           ->paginate(20);
        $distribution_areas = DistributionArea::orderBy('name')->get();
        $threat_categories = ThreatCategory::orderBy('rank')->get();

        return view('livewire.species-distribution-area-manager', [
            'speciesDistributionAreas' => $species_distribution_areas,
            'distribution_areas' => $distribution_areas,
            'threat_categories' => $threat_categories,
        ]);
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(SpeciesDistributionArea $speciesArea )
    {
        $this->speciesArea = $speciesArea;
        $this->form = [
            'distribution_area_id' => $speciesArea->distribution_area_id,
            'status' => $speciesArea->status,
            'threat_category_id' => $speciesArea->threat_category_id,
        ];
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

    
        $formData = $this->form;

        if ($this->speciesArea) {
            $this->speciesArea->update($formData);
            $this->dispatch('notify', message: 'Gebiet aktualisiert');
        } else {
            SpeciesDistributionArea::create(array_merge($formData, [
                'user_id' => auth()->id(),
                'species_id' => $this->species_id,
            ]));
            $this->dispatch('notify', message: 'Gebiet zugewiesen');
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function delete(SpeciesDistributionArea $speciesArea)
    {
        $speciesArea->delete();
        $this->resetPage();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->speciesArea  = null;
        $this->form = [
            'distribution_area_id' => '',
            'status' => 'heimisch',
            'thread_category_id' => ''
        ];
        $this->resetErrorBag();
    }
}
