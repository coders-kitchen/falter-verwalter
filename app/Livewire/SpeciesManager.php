<?php

namespace App\Livewire;

use App\Models\Species;
use App\Models\Family;
use Livewire\Component;
use Livewire\WithPagination;

class SpeciesManager extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $species = null;
    public $form = [
        'name' => '',
        'scientific_name' => '',
        'family_id' => '',
        'size_category' => '',
        'generations_per_year' => '',
        'hibernation_stage' => '',
    ];

    protected $rules = [
        'form.name' => 'required|string|max:255',
        'form.scientific_name' => 'nullable|string|max:255',
        'form.family_id' => 'required|exists:families,id',
        'form.size_category' => 'required|in:XS,S,M,L,XL',
        'form.generations_per_year' => 'nullable|integer|min:1',
        'form.hibernation_stage' => 'nullable|in:egg,larva,pupa,adult',
    ];

    public function render()
    {
        $query = Species::with('family')->orderBy('name');

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('scientific_name', 'like', '%' . $this->search . '%');
        }

        return view('livewire.species-manager', [
            'items' => $query->paginate(50),
            'families' => Family::orderBy('name')->get(),
        ]);
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(Species $species)
    {
        $this->species = $species;
        $this->form = $species->only('name', 'scientific_name', 'family_id', 'size_category', 'generations_per_year', 'hibernation_stage');
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function save()
    {
        $this->validate();

        if ($this->species) {
            $this->species->update($this->form);
            $this->dispatch('notify', message: 'Art aktualisiert');
        } else {
            Species::create(array_merge($this->form, ['user_id' => auth()->id()]));
            $this->dispatch('notify', message: 'Art erstellt');
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function delete(Species $species)
    {
        $species->delete();
        $this->dispatch('notify', message: 'Art gelöscht');
        $this->resetPage();
    }

    public function resetForm()
    {
        $this->form = [
            'name' => '',
            'scientific_name' => '',
            'family_id' => '',
            'size_category' => '',
            'generations_per_year' => '',
            'hibernation_stage' => '',
        ];
        $this->species = null;
    }
}
